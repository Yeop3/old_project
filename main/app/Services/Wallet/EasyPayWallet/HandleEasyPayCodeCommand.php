<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

use App\Models\Client;
use App\Models\Shift;
use App\Models\Wallet\EasyPayTransaction;
use App\Services\Order\OrderPaymentHandler;
use App\Services\Wallet\EasyPayWallet\Exceptions\WrongEasyPayCodeException;
use App\Services\Wallet\EasyPayWallet\TransactionChecker\TransactionCheckerFactory;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Log;
use Money\Currency;
use Money\Money;
use Throwable;

/**
 * Class HandleEasyPayCodeCommand.
 */
final class HandleEasyPayCodeCommand
{
    private OrderPaymentHandler $orderPaymentHandler;
    private EasyPayTransactionByCodeCommand $easyPayTransactionByCodeCommand;
    private TransactionCheckerFactory $checkerFactory;

    /**
     * HandleEasyPayCodeCommand constructor.
     *
     * @param OrderPaymentHandler             $orderPaymentHandler
     * @param EasyPayTransactionByCodeCommand $easyPayTransactionByCodeCommand
     * @param TransactionCheckerFactory       $checkerFactory
     */
    public function __construct(
        OrderPaymentHandler $orderPaymentHandler,
        EasyPayTransactionByCodeCommand $easyPayTransactionByCodeCommand,
        TransactionCheckerFactory $checkerFactory
    ) {
        $this->orderPaymentHandler = $orderPaymentHandler;
        $this->easyPayTransactionByCodeCommand = $easyPayTransactionByCodeCommand;
        $this->checkerFactory = $checkerFactory;
    }

    /**
     * @param Client $client
     * @param string $code
     *
     * @throws Throwable
     */
    public function execute(Client $client, string $code): void
    {
        if (!$client->order) {
            return;
        }

        if ($client->order->getWalletType()->getValue() !== WalletType::TYPE_EASY_PAY) {
            return;
        }

        $length = mb_strlen($code);

        if (!is_numeric($code) && ($length !== 9 || $length !== 10)) {
            throw new WrongEasyPayCodeException('Wrong Code');
        }

        try {
            $transactionChecker = $this->checkerFactory->createByPaymentMethod($client->order->payment_method);
        } catch (InvalidArgumentException $e) {
            return;
        }

        $transactionExists = EasyPayTransaction::whereSellerId($client->seller_id)
            ->where('transaction_id', $code)
            ->exists();

        if ($transactionExists) {
            throw new WrongEasyPayCodeException('Код уже использован');
        }

        $transaction = $this->easyPayTransactionByCodeCommand->execute(
            $code,
            $transactionChecker,
            $client->order
        );

        if (!$transaction) {
            throw new WrongEasyPayCodeException('wrong code');
        }

        $amount = new Money($transaction['amount'] * 100, new Currency('UAH'));

        Log::channel('easy_pay_checks')
            ->info('good code', [
                'client_id' => $client->id,
                'order_id'  => $client->order->id ?? null,
                'code'      => $code,
            ]);

        DB::beginTransaction();

        $this->storeTransaction($client, $amount, $code);

        $this->orderPaymentHandler->handle($client->order, $amount);

        DB::commit();
    }

    /**
     * @param Client $client
     * @param Money  $amount
     * @param string $code
     */
    private function storeTransaction(Client $client, Money $amount, string $code): void
    {
        $shift = Shift::whereSellerId($client->order->seller_id)->whereNull('ended_at')->first();

        $transaction = new EasyPayTransaction();

        $transaction->seller_id = $client->order->seller_id;
        $transaction->easy_pay_wallet_id = $client->order->wallet->id;
        $transaction->order_id = $client->order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $amount;
        $transaction->transaction_id = $code;

        $transaction->save();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney;

use App\Models\Client;
use App\Models\Shift;
use App\Models\Wallet\GlobalMoneyTransaction;
use App\Services\Order\OrderPaymentHandler;
use App\Services\Wallet\GlobalMoney\Exceptions\WrongGlobalMoneyCodeException;
use App\Services\Wallet\GlobalMoney\TransactionChecker\TransactionCheckerFactory;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;

/**
 * Class HandleGlobalMoneyCodeCommand.
 */
final class HandleGlobalMoneyCodeCommand
{
    private OrderPaymentHandler $orderPaymentHandler;
    private GetGlobalMoneyTransactionByCodeCommand $getGlobalMoneyTransactionByCodeCommand;
    private TransactionCheckerFactory $checkerFactory;

    public function __construct(GetGlobalMoneyTransactionByCodeCommand $getGlobalMoneyTransactionByCodeCommand, TransactionCheckerFactory $checkerFactory, OrderPaymentHandler $orderPaymentHandler)
    {
        $this->orderPaymentHandler = $orderPaymentHandler;
        $this->getGlobalMoneyTransactionByCodeCommand = $getGlobalMoneyTransactionByCodeCommand;
        $this->checkerFactory = $checkerFactory;
    }

    public function execute(Client $client, string $code): void
    {
        if (!$client->order) {
            return;
        }

        if ($client->order->getWalletType()->getValue() !== WalletType::TYPE_GLOBAL_MONEY) {
            return;
        }

        $length = mb_strlen($code);

        if (!is_numeric($code) || $length < 5 || $length > 12) {
            throw new WrongGlobalMoneyCodeException();
        }

        try {
            $transactionChecker = $this->checkerFactory->createByPaymentMethod($client->order->payment_method);
        } catch (InvalidArgumentException $e) {
            return;
        }

        $transactionExists = GlobalMoneyTransaction::whereSellerId($client->seller_id)
            ->where('transaction_id', $code)
            ->exists();

        if ($transactionExists) {
            throw new WrongGlobalMoneyCodeException();
        }

        $transaction = $this->getGlobalMoneyTransactionByCodeCommand->execute(
            $code,
            $transactionChecker,
            $client->order->wallet->login_data,
            null,
            $client->order->created_at
        );

        if (!$transaction) {
            throw new WrongGlobalMoneyCodeException();
        }

        $amount = new Money($transaction['amount'], new Currency('UAH'));

        DB::beginTransaction();

        $this->storeTransaction($client, $amount, $code);

        $this->orderPaymentHandler->handle($client->order, $amount);

        DB::commit();
    }

    private function storeTransaction(Client $client, Money $amount, string $code): void
    {
        $shift = Shift::whereSellerId($client->order->seller_id)->whereNull('ended_at')->first();

        $transaction = new GlobalMoneyTransaction();

        $transaction->seller_id = $client->order->seller_id;
        $transaction->global_money_wallet_id = $client->order->wallet->id;
        $transaction->order_id = $client->order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $amount;
        $transaction->transaction_id = $code;

        $transaction->save();
    }
}

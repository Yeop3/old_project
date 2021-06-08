<?php

declare(strict_types=1);

namespace App\Services\Wallet\Crypto;

use App\Models\Order;
use App\Models\Shift;
use App\Models\Wallet\CryptoTransaction;
use App\Models\Wallet\CryptoWallet;
use App\Services\Order\OrderPaymentHandler;
use App\Services\Wallet\Ethereum\GetEthereumWalletInfoFromBlockCypher;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Log;
use Money\Money;

/**
 * Class CheckRotateEthereumWalletPaymentCommand.
 */
final class CheckRotateEthereumWalletPaymentCommand
{
    private GetEthereumWalletInfoFromBlockCypher $getEthereumWalletInfoFromBlockCypher;
    private OrderPaymentHandler $orderPaymentHandler;

    public function __construct(GetEthereumWalletInfoFromBlockCypher $getEthereumWalletInfoFromBlockCypher, OrderPaymentHandler $orderPaymentHandler)
    {
        $this->getEthereumWalletInfoFromBlockCypher = $getEthereumWalletInfoFromBlockCypher;
        $this->orderPaymentHandler = $orderPaymentHandler;
    }

    public function execute(Order $order): void
    {
        if (!$order->wallet) {
            return;
        }

        if (!$order->wallet->isCrypto()) {
            return;
        }

        if ($order->wallet->currency->getValue() !== 'eth') {
            return;
        }

        if ($order->wallet->payment_type->getValue() !== CryptoWalletPaymentType::ROTATE) {
            return;
        }

        if (!$order->wallet->ordersWaitingPayment->count()) {
            return;
        }

        $order = $order->wallet->ordersWaitingPayment->first();

        if (!$order) {
            return;
        }

        try {
            $walletInfo = $this->getEthereumWalletInfoFromBlockCypher->execute($order->wallet->address, $order->wallet->confirmations, $order->wallet->proxy);
        } catch (BadResponseException $e) {
            Log::channel('crypto_checks')->error(
                json_encode(
                    [
                        $e->getMessage(),
                        $e->getResponse()->getBody()->getContents(),
                    ],
                    JSON_PRETTY_PRINT
                )
            );

            return;
        }

        $orderTransactions = CryptoTransaction::whereOrderId($order->id)->get();

        $hashes = $orderTransactions->pluck('hash')->toArray();

        $transactions = $this->filterTransactions($walletInfo['txrefs'], $order, $hashes);

        if (!$transactions->count()) {
            return;
        }

        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        foreach ($transactions as $incomingTransaction) {
            DB::beginTransaction();

            $this->storeTransaction($incomingTransaction, $order->wallet, $order, $shift);

            DB::commit();
        }
    }

    private function filterTransactions(array $txrefs, Order $order, array $hashes): Collection
    {
        return collect($txrefs)
            ->filter(function (array $transaction) use ($order, $hashes) {
                if (in_array($transaction['tx_hash'], $hashes, true)) {
                    return false;
                }

                $confirmed = Carbon::parse($transaction['confirmed'], 'UTC');
                $confirmed->setTimezone(config('app.timezone'));

                return $order->created_at->lt($confirmed);
            })
            ->values();
    }

    private function storeTransaction(array $incomingTransaction, CryptoWallet $wallet, Order $order, Shift $shift): void
    {
        $amount = (int) mb_strimwidth((string) $incomingTransaction['value'], 0, 8);

        $currency = $wallet->getCurrency();

        $cryptoAmount = new Money($amount, $currency);

        $uahAmount = convertToRubFromCrypto($cryptoAmount, $currency, (float) $order->crypto_uah_rate);

        $this->orderPaymentHandler->handle($order, $uahAmount);

        $transaction = new CryptoTransaction();

        $transaction->seller_id = $order->seller_id;
        $transaction->crypto_wallet_id = $order->wallet->id;
        $transaction->order_id = $order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $uahAmount;
        $transaction->hash = $incomingTransaction['tx_hash'];

        $transaction->save();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

use App\Models\Order;
use App\Models\Wallet\EasyPayWallet;
use App\Services\Wallet\EasyPayWallet\TransactionChecker\TransactionChecker;
use App\Services\Wallet\EasyPayWallet\VO\DateRange;
use App\Services\Wallet\EasyPayWallet\VO\PageData;
use App\Services\Wallet\VO\PaymentMethod;

/**
 * Class EasyPayTransactionByCodeCommand.
 */
final class EasyPayTransactionByCodeCommand
{
    private EasyPayApi $api;
    private int $tries = 0;

    private const MAX_TRIES = 10;

    /**
     * EasyPayTransactionByCodeCommand constructor.
     *
     * @param EasyPayApi $api
     */
    public function __construct(EasyPayApi $api)
    {
        $this->api = $api;
    }

    public function execute(
        string $code,
        TransactionChecker $transactionChecker,
        Order $order
    ): ?array {
        $this->tries++;

        $this->api->setProxy($order->wallet->proxy);
        $this->api->setLoginData($order->wallet->login_data);

        if (!$this->api->isLoggedIn()) {
            $this->api->login();
        }

        /* @var EasyPayWallet $wallet */
        $wallet = $order->wallet;

        if ($order->payment_method->getValue() === PaymentMethod::EASY_PAY_ONLINE) {
            $transaction = $this->api->getWalletTransactions(
                $wallet->instrument_id,
                new DateRange($order->created_at, now()->addDay()),
                new PageData(1)
            );

            if (count($transaction['items']) === 0) {
                return null;
            }

            $transaction = $this->easyMoneyOnline($transaction, $code, $transactionChecker, $order);

            return $transaction;
        }

        if ($order->payment_method->getValue() === PaymentMethod::EASY_PAY_TERMINAL) {
            $transaction = $this->api->getWalletStatements(
                $wallet->external_id,
                $order->created_at->month,
                $order->created_at->year
            );

            if (count($transaction) === 0) {
                return null;
            }

            return $this->easyMoneyTerminal($transaction, $code, $transactionChecker, $order);
        }

        return null;
    }

    /**
     * @param array              $transactions
     * @param string             $code
     * @param TransactionChecker $transactionChecker
     * @param Order              $order
     *
     * @return array|null
     */
    private function easyMoneyOnline(array $transactions, string $code, TransactionChecker $transactionChecker, Order $order): ?array
    {
        $transactionItems = ($transactions['items']);

        $foundTransaction = null;

        foreach ($transactionItems as $item) {
            if ($transactionChecker->check($item, $code, $order->seller_id)) {
                $foundTransaction = $item;
                break;
            }

            if (!$foundTransaction && $this->tries <= self::MAX_TRIES) {
                $lastTransaction = end($transactionItems);
                $lastDate = carbonSafeParse($lastTransaction['datePost'] ?? null);

                if (!$lastDate) {
                    return null;
                }

                return $this->execute($code, $transactionChecker, $order);
            }
        }

        return $foundTransaction;
    }

    /**
     * @param array              $transactions
     * @param string             $code
     * @param TransactionChecker $transactionChecker
     * @param Order              $order
     *
     * @return array|null
     */
    private function easyMoneyTerminal(array $transactions, string $code, TransactionChecker $transactionChecker, Order $order): ?array
    {
        $foundTransaction = null;
        foreach ($transactions as $item) {
            if ($transactionChecker->check($item, $code, $order->seller_id)) {
                $foundTransaction = $item;
                break;
            }

            if (!$transactions && $this->tries <= self::MAX_TRIES) {
                $lastTransaction = end($transactions);
                $lastDate = carbonSafeParse($lastTransaction['datePost']);

                if (!$lastDate) {
                    return null;
                }

                return $this->execute($code, $transactionChecker, $order);
            }
        }

        return $foundTransaction;
    }
}

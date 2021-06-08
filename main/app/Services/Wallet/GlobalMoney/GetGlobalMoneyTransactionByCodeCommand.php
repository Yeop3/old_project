<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney;

use App\Services\Wallet\GlobalMoney\TransactionChecker\TransactionChecker;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Carbon\Carbon;

/**
 * Class GetGlobalMoneyTransactionByCodeCommand.
 */
final class GetGlobalMoneyTransactionByCodeCommand
{
    private GlobalMoneyApi $api;
    private int $tries = 0;

    private const MAX_TRIES = 10;

    public function __construct(GlobalMoneyApi $api)
    {
        $this->api = $api;
    }

    public function execute(
        string $code,
        TransactionChecker $transactionChecker,
        GlobalMoneyLoginData $loginData,
        ?Carbon $dateTill = null,
        ?Carbon $dateAfter = null
    ): ?array {
        $this->tries++;

        $this->api->setLoginData($loginData);

        if (!$this->api->isLoggedIn()) {
            $this->api->login();
        }

        $transactions = $this->api->getTransactions($dateTill);

        if (count($transactions) === 0) {
            return null;
        }

        $foundTransaction = null;

        foreach ($transactions as $transaction) {
            $date = carbonSafeParse($transaction['timestamp']);

            if ($dateAfter && $date <= $dateAfter) {
                return null;
            }

            if ($transactionChecker->check($transaction, $code)) {
                $foundTransaction = $transaction;
                break;
            }
        }

        if (!$foundTransaction && $this->tries <= self::MAX_TRIES) {
            $lastTransaction = end($transactions);
            $lastDate = carbonSafeParse($lastTransaction['timestamp']);

            if (!$lastDate) {
                return null;
            }

            return $this->execute($code, $transactionChecker, $loginData, $lastDate, $dateAfter);
        }

        return $foundTransaction;
    }
}

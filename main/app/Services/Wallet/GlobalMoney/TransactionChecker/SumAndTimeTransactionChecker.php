<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\TransactionChecker;

use Money\Currency;
use Money\Money;

/**
 * Class SumAndTimeTransactionChecker.
 */
final class SumAndTimeTransactionChecker implements TransactionChecker
{
    public function check(array $transaction, string $code): bool
    {
        $date = carbonSafeParse($transaction['timestamp']);

        if (
            !$date
            || $date < now()->subDay()
        ) {
            return false;
        }

        $timePos = mb_strlen($code) - 4;
        $sum = mb_substr($code, 0, $timePos);

        $sumAmount = new Money($sum * 100, new Currency('UAH'));

        $amount = new Money($transaction['amount'], new Currency('UAH'));

        if (!$amount->equals($sumAmount)) {
            return false;
        }

        $time = mb_substr($code, -4);
        $hours = mb_substr($time, 0, 2);
        $minutes = mb_substr($time, 2, 2);

        return !($date->hour !== intval($hours) || $date->minute !== intval($minutes));
    }
}

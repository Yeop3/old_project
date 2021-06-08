<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\TransactionChecker;

/**
 * Class CardTransactionChecker.
 */
final class CardTransactionChecker implements TransactionChecker
{
    public function check(array $transaction, string $code): bool
    {
        return mb_stristr($transaction['comment'] ?? '', $code) !== false;
    }
}

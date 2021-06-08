<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\TransactionChecker;

/**
 * Class IdTransactionChecker.
 */
final class IdTransactionChecker implements TransactionChecker
{
    public function check(array $transaction, string $code): bool
    {
        return (int) $transaction['id'] === (int) $code;
    }
}

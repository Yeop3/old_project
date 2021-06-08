<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\TransactionChecker;

/**
 * Interface TransactionChecker.
 */
interface TransactionChecker
{
    public function check(array $transaction, string $code): bool;
}

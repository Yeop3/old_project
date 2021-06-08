<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\TransactionChecker;

/**
 * Interface TransactionChecker.
 */
interface TransactionChecker
{
    public function check(array $transaction, string $code, ?int $sellerId = null): bool;
}

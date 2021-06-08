<?php

namespace App\Services\Wallet\EasyPayWallet\TransactionChecker;

use App\Models\Wallet\EasyPayTransaction;

/**
 * Class IdTransactionChecker.
 */
class IdTransactionChecker implements TransactionChecker
{
    public function check(array $transaction, string $code, ?int $sellerId = null): bool
    {
        $transactionExists = EasyPayTransaction::whereSellerId($sellerId)
            ->where('transaction_id', $code)
            ->exists();

        if ($transactionExists) {
            return false;
        }

        return (int) $transaction['transactionId'] === (int) $code;
    }
}

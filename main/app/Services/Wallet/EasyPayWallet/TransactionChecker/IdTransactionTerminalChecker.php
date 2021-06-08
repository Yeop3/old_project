<?php

namespace App\Services\Wallet\EasyPayWallet\TransactionChecker;

use App\Models\Wallet\EasyPayTransaction;

/**
 * Class IdTransactionTerminalChecker.
 */
class IdTransactionTerminalChecker implements TransactionChecker
{
    public function check(array $transaction, string $code, ?int $sellerId = null): bool
    {
        $transactionTerminalNumber = preg_replace(
            '/[^0-9]/',
            '',
            $transaction['paymentDetail']
        );

        $codeLength = mb_strlen($code);

        $codeTerminalNumber = mb_substr($code, 0, $codeLength - 4);

        if ($codeTerminalNumber !== $transactionTerminalNumber) {
            return false;
        }

        $time = mb_substr($code, -4);
        $hours = mb_substr($time, 0, 2);
        $minutes = mb_substr($time, 2, 2);

        $transactionDate = carbonSafeParse($transaction['dateAccept']);
        $codeDate = $transactionDate
            ->copy()
            ->setHour((int) $hours)
            ->setMinutes((int) $minutes);

        if (abs($transactionDate->diffInMinutes($codeDate)) > 1) {
            return false;
        }

        $codes = [$code];

        $codeDatePlus1 = $transactionDate
            ->copy()
            ->setHour((int) $hours)
            ->setMinutes((int) $minutes)
            ->addMinute();

        $codes[] = mb_substr($code, 0, mb_strlen($code) - 4).$codeDatePlus1->format('Hi');

        $codeDateMinus1 = $transactionDate
            ->copy()
            ->setHour((int) $hours)
            ->setMinutes((int) $minutes)
            ->subMinute();

        $codes[] = mb_substr($code, 0, mb_strlen($code) - 4).$codeDateMinus1->format('Hi');

        $transactionExists = EasyPayTransaction::whereSellerId($sellerId)
            ->whereIn('transaction_id', $codes)
            ->exists();

        if ($transactionExists) {
            return false;
        }

        return true;
    }
}

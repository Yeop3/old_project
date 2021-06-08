<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\TransactionChecker;

use App\Services\Wallet\VO\PaymentMethod;
use InvalidArgumentException;

/**
 * Class TransactionCheckerFactory.
 */
final class TransactionCheckerFactory
{
    private const MAP = [
        PaymentMethod::GLOBAL_MONEY_CARD     => CardTransactionChecker::class,
        PaymentMethod::GLOBAL_MONEY_ONLINE   => IdTransactionChecker::class,
        PaymentMethod::GLOBAL_MONEY_TERMINAL => SumAndTimeTransactionChecker::class,
    ];

    public function createByPaymentMethod(PaymentMethod $paymentMethod): TransactionChecker
    {
        $checkerClass = self::MAP[$paymentMethod->getValue()] ?? null;

        if (!$checkerClass) {
            throw new InvalidArgumentException('Unknown payment method for transaction checker creation');
        }

        return new $checkerClass();
    }
}

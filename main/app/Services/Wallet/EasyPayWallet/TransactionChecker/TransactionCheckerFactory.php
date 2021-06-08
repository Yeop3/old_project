<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\TransactionChecker;

use App\Services\Wallet\VO\PaymentMethod;
use InvalidArgumentException;

/**
 * Class TransactionCheckerFactory.
 */
final class TransactionCheckerFactory
{
    private const MAP = [
        PaymentMethod::EASY_PAY_ONLINE   => IdTransactionChecker::class,
        PaymentMethod::EASY_PAY_TERMINAL => IdTransactionTerminalChecker::class,
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

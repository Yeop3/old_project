<?php

declare(strict_types=1);

namespace App\Models\Wallet;

use App\Models\Order;
use App\Services\Wallet\VO\WalletType;
use Money\Currency;
use Money\Money;

/**
 * Interface Wallet.
 */
interface Wallet
{
    public function replaceTextVars(string $text): string;

    public function fillOrderInfo(Order $order): void;

    public function getCurrency(): Currency;

    public function isCrypto(): bool;

    public function getWalletType(): WalletType;

    public function createPaymentForGiven(Order $order, Money $amount): void;
}

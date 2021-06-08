<?php

declare(strict_types=1);

namespace App\Services\Order\CommissionCalculator;

use App\VO\Commission;
use App\VO\CommissionType;
use Money\Currency;
use Money\Money;

/**
 * Class CommissionCalculator.
 */
final class CommissionCalculator
{
    /**
     * @param Money        $price
     * @param Commission[] $commissions
     *
     * @return Money
     */
    public function calc(Money $price, array $commissions): Money
    {
        $calculatedPrice = clone $price;

        foreach ($commissions as $commission) {
            $calculatedPrice = $this->applyCommission($price, $calculatedPrice, $commission);
        }

        return $calculatedPrice->subtract($price);
    }

    private function applyCommission(Money $price, Money $calculatedPrice, Commission $commission): Money
    {
        if ($commission->getType()->getValue() === CommissionType::TYPE_PERCENT) {
            $commissionAmount = $price->divide(100)->multiply($commission->getValue());

            return $calculatedPrice->add($commissionAmount);
        }

        return $calculatedPrice->add(new Money($commission->getValue() * 100, new Currency('UAH')));
    }
}

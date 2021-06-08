<?php

declare(strict_types=1);

namespace App\Services\Discount\Calculator;

use App\Services\Discount\VO\DiscountDateRange;
use Illuminate\Support\Collection;

/**
 * Class DiscountCalculator.
 */
final class DiscountCalculator
{
    /**
     * @param DiscountCalculatorItem[] $discounts
     *
     * @return DiscountCalculatorItem
     */
    public function determineHighestDiscount(array $discounts): DiscountCalculatorItem
    {
        if (count($discounts) === 0) {
            return new DiscountCalculatorItem(0.0, 0);
        }

        /** @var Collection $discounts */
        $discounts = collect($discounts)
            ->filter(fn (DiscountCalculatorItem $item) => $this->checkDiscountIsActual($item->getDateRange()));

        $highestPriority = $discounts->max(fn (DiscountCalculatorItem $item) => $item->getPriority());

        $discountsWithHighestPriority = $discounts
            ->filter(fn (DiscountCalculatorItem $item) => $item->getPriority() === $highestPriority)
            ->values();

        $maxValue = $discountsWithHighestPriority->max(fn (DiscountCalculatorItem $item) => $item->getValue());

        return $discounts->filter(fn (DiscountCalculatorItem $item) => $item->getValue() === $maxValue)->first();
    }

    private function checkDiscountIsActual(?DiscountDateRange $dateRange = null): bool
    {
        if ($dateRange === null) {
            return true;
        }

        if ($dateRange->getEnd() === null) {
            return now() >= $dateRange->getStart();
        }

        return now() >= $dateRange->getStart() && now() < $dateRange->getEnd();
    }
}

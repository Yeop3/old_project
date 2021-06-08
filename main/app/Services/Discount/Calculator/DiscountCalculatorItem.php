<?php

declare(strict_types=1);

namespace App\Services\Discount\Calculator;

use App\Services\Discount\VO\DiscountDateRange;

/**
 * Class DiscountCalculatorItem.
 */
final class DiscountCalculatorItem
{
    private float $value;
    private int $priority;
    private ?DiscountDateRange $dateRange;
    private ?int               $discountId;

    public function __construct(float $value, int $priority, ?DiscountDateRange $dateRange = null, ?int $discountId = null)
    {
        $this->value = $value;
        $this->priority = $priority;
        $this->dateRange = $dateRange;
        $this->discountId = $discountId;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function toArray(): array
    {
        return [
            'value'    => $this->value,
            'priority' => $this->priority,
        ];
    }

    public function getDateRange(): ?DiscountDateRange
    {
        return $this->dateRange;
    }

    public function getDiscountId(): ?int
    {
        return $this->discountId;
    }
}

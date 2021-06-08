<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Discount\Calculator\DiscountCalculatorItem;
use Illuminate\Support\Collection;

/**
 * Interface Discountable.
 */
interface Discountable
{
    /**
     * @return DiscountCalculatorItem[]|Collection
     */
    public function getDiscountCalculatorItems(): Collection;
}

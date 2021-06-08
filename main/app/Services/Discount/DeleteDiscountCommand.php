<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\Seller;

/**
 * Class DeleteDiscountCommand.
 */
final class DeleteDiscountCommand
{
    public function execute(int $discountNumber, Seller $seller): void
    {
        $discount = Discount::whereSellerId($seller->id)->whereNumber($discountNumber)->firstOrFail();

        $discount->delete();
    }
}

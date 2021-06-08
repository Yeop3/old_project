<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateDiscountCommand.
 */
final class UpdateDiscountCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $discountNumber, Seller $seller, DiscountDto $dto): Discount
    {
        $discount = Discount::whereSellerId($seller->id)->whereNumber($discountNumber)->firstOrFail();

        $this->checker->fillInfo($dto, $discount);

        DB::beginTransaction();

        $discount->save();

        $this->checker->syncProductTypes($seller, $dto, $discount);

        $this->checker->syncLocations($seller, $dto, $discount);

        DB::commit();

        return $discount;
    }
}

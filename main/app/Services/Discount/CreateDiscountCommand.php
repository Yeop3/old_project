<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateDiscountCommand.
 */
final class CreateDiscountCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(Seller $seller, DiscountDto $dto): Discount
    {
        $discount = new Discount();

        $discount->seller_id = $seller->id;
        $this->checker->fillInfo($dto, $discount);

        DB::beginTransaction();

        $discount->save();

        $this->checker->syncProductTypes($seller, $dto, $discount);

        $this->checker->syncLocations($seller, $dto, $discount);

        DB::commit();

        return $discount;
    }
}

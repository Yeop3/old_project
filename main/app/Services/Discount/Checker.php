<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\Location;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\Discount\Exceptions\DiscountShouldHasMinOneLocationException;
use App\Services\Discount\Exceptions\DiscountShouldHasMinOneProductTypeException;

/**
 * Class Checker.
 */
final class Checker
{
    public function fillInfo(DiscountDto $dto, Discount $discount): void
    {
        $discount->name = $dto->getName();
        $discount->description = $dto->getDescription();
        $discount->discount_value = $dto->getDiscountValue();
        $discount->discount_priority = $dto->getDiscountPriority();
        $discount->active = $dto->isActive();
        $discount->date_range = $dto->getDateRange();
        $discount->client_min_paid_orders_count = $dto->getClientMinPaidOrdersCount();
        $discount->client_min_income = $dto->getClientMinIncome();
    }

    public function syncProductTypes(Seller $seller, DiscountDto $dto, Discount $discount): void
    {
        $productTypes = ProductType::whereSellerId($seller->id)
            ->whereIn('number', $dto->getProductTypeNumbers())
            ->get();

        if ($productTypes->count() < 1) {
            throw new DiscountShouldHasMinOneProductTypeException('Discount should has min one product type');
        }

        $discount->productTypes()->sync($productTypes->pluck('id'));
    }

    public function syncLocations(Seller $seller, DiscountDto $dto, Discount $discount): void
    {
        $locations = Location::whereSellerId($seller->id)
            ->whereIn('number', $dto->getLocationNumbers())
            ->whereDoesntHave('children')
            ->get();

        if ($locations->count() < 1) {
            throw new DiscountShouldHasMinOneLocationException('Discount should has min one location');
        }

        $discount->locations()->sync($locations->pluck('id'));
    }
}

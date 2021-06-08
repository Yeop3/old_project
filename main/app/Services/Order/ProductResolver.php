<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;

/**
 * Class ProductResolver.
 */
final class ProductResolver
{
    public function resolve(ProductType $productType, Location $location): ?Product
    {
        return Product::whereProductTypeId($productType->id)
            ->whereLocationId($location->id)
            ->active()
            ->orderBy('number', 'asc')
            ->first();
    }
}

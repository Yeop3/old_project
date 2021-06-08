<?php

declare(strict_types=1);

namespace App\Services\ProductType;

use App\Models\Driver;
use App\Models\Seller;
use App\Services\ProductType\Exceptions\ProductTypeExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkDuplicate(Seller $seller, ProductTypeDto $dto, ?int $exceptNumber = null): void
    {
        $name = $dto->getName();

        $driverExists = Driver::whereSellerId($seller->id)
            ->whereName($name)
            ->when($exceptNumber, fn (Builder $query) => $query->where('number', '!=', $exceptNumber))
            ->exists();

        if ($driverExists) {
            throw new ProductTypeExistsException("Product type with name $name already exists");
        }
    }
}

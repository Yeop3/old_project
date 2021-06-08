<?php

declare(strict_types=1);

namespace App\Services\Location;

use App\Models\Location;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Location\Exceptions\LocationExistsException;
use App\Services\Product\Exceptions\LocationParentProductExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{

    /**
     * @param Seller $seller
     * @param LocationDto $dto
     * @param int|null $parentLocationId
     * @param int|null $exceptNumber
     */
    public function checkDuplicate(
        Seller $seller,
        LocationDto $dto,
        ?int $parentLocationId = null,
        ?int $exceptNumber = null
    ): void
    {
        $name = $dto->getName();

        $locationExists = Location::whereSellerId($seller->id)
            ->whereName($name)
            ->when(
                $exceptNumber,
                fn (Builder $query) => $query->where('number', '!=', $exceptNumber)
            )
            ->when(
                $parentLocationId,
                fn (Builder $query) => $query->where('parent_id', $parentLocationId)
            )
            ->exists();

        if ($locationExists) {
            throw new LocationExistsException('Такая локация уже существует');
        }
    }

    /**
     * @param Location $parentLocation
     */
    public function checkProductParent(Location $parentLocation): void
    {
        if (!$parentLocation) {
            return;
        }

        $productExist = Product::whereLocationId($parentLocation->id)->exists();

        if ($productExist) {
            throw new LocationParentProductExistsException('В родительской локации не должно быть товаров');
        }
    }
}

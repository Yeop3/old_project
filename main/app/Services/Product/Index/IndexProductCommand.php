<?php

namespace App\Services\Product\Index;

use App\Models\Driver;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Class IndexProductCommand.
 */
final class IndexProductCommand
{
    /**
     * @param IndexProductDto $dto
     *
     * @return LengthAwarePaginator
     */
    public function execute(IndexProductDto $dto): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        $product = Product::whereSellerId($user->seller_id);

        if ($dto->getLocationId()) {
            $location = Location::whereSellerId($user->seller_id)->whereNumber($dto->getLocationId())->first();
            if ($location) {
                $product->where('location_id', $location->id);
            }
        }

        if ($dto->getDriverId()) {
            $driver = Driver::whereSellerId($user->seller_id)->whereNumber($dto->getDriverId())->first();
            if ($driver) {
                $product->where('driver_id', $driver->id);
            }
        }

        if ($dto->getProductTypeId()) {
            $productType = ProductType::whereSellerId($user->seller_id)->whereNumber($dto->getProductTypeId())->first();
            if ($productType) {
                $product->where('product_type_id', $productType->id);
            }
        }

        if ($dto->getaddress()) {
            $product->where('address', 'like', "%{$dto->getAddress()}%");
        }

        if ($dto->getNumber()) {
            $product->where('number', $dto->getNumber());
        }

        if ($dto->getStatus()) {
            $product->where('status', $dto->getStatus());
        }

        $sortFields = [
            'number',
            'product_type_id',
            'location_id',
            'status',
            'driver_id',
            'created_at',
        ];

        if (in_array($dto->getSortField(), $sortFields, true)) {
            if ($dto->getSortDirection() === 'asc') {
                $product->orderBy($dto->getSortField());
            }

            if ($dto->getSortDirection() === 'desc') {
                $product->orderByDesc($dto->getSortField());
            }
        }

        if ($dto->getCoordinates()) {
            $product->where('coordinates', 'like', "%{$dto->getCoordinates()}%");
        }

        if ($dto->getComission()) {
            $product->where('commission_value', $dto->getComission());
        }

        if ($dto->getCreatedAt()) {
            $date = new Carbon($dto->getCreatedAt());
            $product->whereDate('created_at', $date);
        }

        $product = $product->with([
            'driver',
            'productType',
            'location',
            'location.ancestors',
            'photos',
        ])->paginate(20);
        $product->getCollection()->each(function (Product $product) {
            $product->location->append(['name_chain']);

            return $product;
        });

        return $product;
    }
}

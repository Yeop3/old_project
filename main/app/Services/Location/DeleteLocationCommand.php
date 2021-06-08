<?php

declare(strict_types=1);

namespace App\Services\Location;

use App\Models\Location;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Location\Exceptions\CantDeleteLocationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class DeleteLocationCommand.
 */
final class DeleteLocationCommand
{
    public function execute(int $locationId, Seller $seller): void
    {
        $location = Location::whereSellerId($seller->id)
            ->whereNumber($locationId)
            ->with('descendants')
            ->firstOrFail();

        $locationNumbers = $location->descendants->pluck('id')->push($location->number)->toArray();

        if (Product::whereIn('location_id', $locationNumbers)->exists()) {
            throw new CantDeleteLocationException("Не возможно удалить т.к. локация $location->name либо её потомки привязаны к кладам");
        }

        $location->delete();
    }

    /**
     * @param int[]  $ids
     * @param Seller $seller
     *
     * @return int[]
     */
    public function executeMass(array $ids, Seller $seller): array
    {
        $cantDeleteIds = [];

        foreach ($ids as $id) {
            try {
                $this->execute($id, $seller);
            } catch (CantDeleteLocationException $e) {
                $cantDeleteIds[] = $id;
            } catch (ModelNotFoundException $e) {
                continue;
            }
        }

        return $cantDeleteIds;
    }
}

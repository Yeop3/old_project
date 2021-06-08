<?php

declare(strict_types=1);

namespace App\Services\Driver;

use App\Models\Driver;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Driver\Exceptions\CantDeleteDriverException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class DeleteDriverCommand.
 */
final class DeleteDriverCommand
{
    public function execute(int $driverId, Seller $seller): void
    {
        $driver = Driver::whereSellerId($seller->id)->whereNumber($driverId)->firstOrFail();

        if (Product::whereDriverId($driver->id)->exists()) {
            throw new CantDeleteDriverException("Не возможно удалить т.к. курьер $driver->name привязан к кладам");
        }

        $driver->delete();
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
            } catch (CantDeleteDriverException $e) {
                $cantDeleteIds[] = $id;
            } catch (ModelNotFoundException $e) {
                continue;
            }
        }

        return $cantDeleteIds;
    }
}

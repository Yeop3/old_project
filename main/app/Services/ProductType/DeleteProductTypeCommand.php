<?php

declare(strict_types=1);

namespace App\Services\ProductType;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\ProductType\Exceptions\CantDeleteProductTypeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class DeleteProductTypeCommand.
 */
final class DeleteProductTypeCommand
{
    public function execute(int $productTypeNumber, Seller $seller): void
    {
        $productType = ProductType::whereSellerId($seller->id)->whereNumber($productTypeNumber)->firstOrFail();

        if (Product::whereProductTypeId($productType->id)->exists()) {
            throw new CantDeleteProductTypeException("Не возможно удалить т.к. $productType->name привязан к кладам");
        }

        $productType->delete();
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
            } catch (CantDeleteProductTypeException $e) {
                $cantDeleteIds[] = $id;
            } catch (ModelNotFoundException $e) {
                continue;
            }
        }

        return $cantDeleteIds;
    }
}

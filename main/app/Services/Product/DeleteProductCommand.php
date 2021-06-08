<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Product\Exceptions\CantDeleteProductException;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class DeleteProductCommand.
 */
final class DeleteProductCommand
{
    public function execute(int $productNumber, Seller $seller): void
    {
        $product = Product::whereSellerId($seller->id)
            ->whereNumber($productNumber)
            ->firstOrFail();

        if ($product->status->getValue() === ProductStatus::STATUS_BOOKED) {
            throw new CantDeleteProductException('Невозможно удалять зарезервированный товар');
        }

        if (Order::whereSellerId($product->seller_id)->whereProductId($product->id)->exists()) {
            throw new CantDeleteProductException('Невозможно удалить, товар привязан к заказу');
        }

        $product->delete();
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
            } catch (ModelNotFoundException | CantDeleteProductException $e) {
                continue;
            }
        }

        return $cantDeleteIds;
    }
}

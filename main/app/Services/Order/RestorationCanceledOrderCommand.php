<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;
use App\Services\Order\Exceptions\CantFindProductForOrderException;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class RestorationCanceledOrderCommand.
 */
class RestorationCanceledOrderCommand
{
    public function execute(int $sellerId, Product $product, Order $canceledOrder): void
    {
        $canceledOrder->status = new OrderStatus(OrderStatus::STATUS_AWAITING_PAYMENT);
        $canceledOrder->canceled_at = null;

        if ($product->status->getValue() === ProductStatus::STATUS_SELL) {
            $canceledOrder->created_at = now();
            $product->status = new ProductStatus(ProductStatus::STATUS_BOOKED);
            $product->booked_at = now();
            $product->save();
            $canceledOrder->save();

            return;
        }

        $newProduct = Product::whereSellerId($sellerId)
            ->where('location_id', $product->location_id)
            ->whereProductTypeId($product->product_type_id)
            ->whereStatus(ProductStatus::STATUS_SELL)
            ->first();

        if (!$newProduct && $product->location->parent) {
            $newProduct = Product::whereSellerId($sellerId)
                ->whereIn('location_id', $product->location->parent->children->pluck('id'))
                ->whereProductTypeId($product->product_type_id)
                ->whereStatus(ProductStatus::STATUS_SELL)
                ->firstOrFail();
        }

        if (!$newProduct) {
            throw new CantFindProductForOrderException('Нет подходящего товара для восстановления.');
        }

        $canceledOrder->product_id = $newProduct->id;
        $canceledOrder->created_at = now();
        $newProduct->status = new ProductStatus(ProductStatus::STATUS_BOOKED);
        $newProduct->booked_at = now();

        try {
            DB::beginTransaction();
            $newProduct->save();
            $canceledOrder->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }
}

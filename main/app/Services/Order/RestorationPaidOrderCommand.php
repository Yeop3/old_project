<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;
use App\Services\Order\Exceptions\CantFindProductForOrderException;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class RestorationPaidOrderCommand.
 */
class RestorationPaidOrderCommand
{
    public function execute(int $sellerId, Product $product, Order $paidOrder): void
    {
        $paidOrder->canceled_at = null;

        $getNewLocation = $product->location->parent ?: collect(['children' => []]);
        $locationsIds = collect([$product->location_id]) + $getNewLocation->children->pluck('id');

        try {
            $newProduct = Product::whereSellerId($sellerId)->whereIn('location_id', $locationsIds->toArray())
                ->whereProductTypeId($product->product_type_id)
                ->whereStatus(ProductStatus::STATUS_SELL)
                ->firstOrFail();

            $paidOrder->status = new OrderStatus(OrderStatus::STATUS_AWAITING_PAYMENT);
            $paidOrder->product_id = $newProduct->id;
            $paidOrder->created_at = Carbon::now();
            $paidOrder->unpaid_amount = $paidOrder->total_price;

            $newProduct->booked_at = Carbon::now();

            $newProduct->status = new ProductStatus(ProductStatus::STATUS_BOOKED);

            DB::beginTransaction();

            $newProduct->save();
            $paidOrder->save();

            DB::commit();
        } catch (Throwable $e) {
            Log::error(json_encode([
                'ExceptionClass' => class_basename($e),
                $e->getMessage(),
                $e->getTrace(),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

            throw new CantFindProductForOrderException(
                'Нет подходящего товара для восстановления.'
            );
        }
    }
}

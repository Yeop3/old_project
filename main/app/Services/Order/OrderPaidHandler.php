<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Events\Order\OrderPaid;
use App\Models\Order;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Events\Dispatcher;
use Money\Currency;
use Money\Money;

/**
 * Class OrderPaidHandler.
 */
final class OrderPaidHandler
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(Order $order, int $status, ?Money $amount = null): void
    {
        if (!$amount) {
            $amount = new Money(0, new Currency('UAH'));
        }

        $order->status = new OrderStatus($status);
        $order->unpaid_amount = $order->unpaid_amount->subtract($amount);

        $order->save();

        if ($status === OrderStatus::STATUS_GIVEN) {
//                if ($order->product->productType->delivery_type->isTaxi()) {
//                    break;
//                }

            $order->product->status = new ProductStatus(ProductStatus::STATUS_SOLD);
            $order->product->save();

            $this->dispatcher->dispatch(new OrderPaid($order));
        }
    }
}

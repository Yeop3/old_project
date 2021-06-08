<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Events\Order\OrderPaid;
use App\Events\Order\OrderPartiallyPaid;
use App\Models\Order;
use App\Services\Order\Exceptions\WrongOrderStatusForPaymentException;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Events\Dispatcher;
use Money\Currency;
use Money\Money;

/**
 * Class OrderPaymentHandler.
 */
final class OrderPaymentHandler
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(Order $order, Money $paidAmount): void
    {
        if (!in_array($order->status->getValue(), [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID], true)) {
            throw new WrongOrderStatusForPaymentException("order $order->id has wrong status for paid - {$order->status->getValue()}");
        }

        if ($order->unpaid_amount->lessThanOrEqual($paidAmount)) {
            $this->handleFullPayment($order, $paidAmount);

            return;
        }

        $this->handlePartiallyPayment($order, $paidAmount);
    }

    private function handlePartiallyPayment(Order $order, Money $paidAmount): void
    {
        $order->status = new OrderStatus(OrderStatus::STATUS_PARTIALLY_PAID);
        $order->unpaid_amount = $order->unpaid_amount->subtract($paidAmount);

        $order->save();

        $this->dispatcher->dispatch(new OrderPartiallyPaid($order));
    }

    private function handleFullPayment(Order $order, Money $paidAmount): void
    {
        $order->status = new OrderStatus(OrderStatus::STATUS_PAID);
        $order->unpaid_amount = new Money(0, new Currency('UAH'));

        $order->product->status = new ProductStatus(ProductStatus::STATUS_SOLD);
        $order->completed_at = now();
        $order->product->save();

        $order->save();

        $this->dispatcher->dispatch(new OrderPaid($order));
    }
}

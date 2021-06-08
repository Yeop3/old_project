<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Services\Order\Exceptions\CantCancelOrderException;
use App\Services\Order\VO\CancelInitiator;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class CancelOrderCommand.
 */
final class CancelOrderCommand
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function execute(Order $order, CancelInitiator $cancelInitiator, bool $allowToCancelPartiallyPaidOrders = false): void
    {
        $this->check($order, $cancelInitiator, $allowToCancelPartiallyPaidOrders);

        DB::beginTransaction();

        $order->status = $cancelInitiator->getStatus();

        $order->save();

        $order->product->status = new ProductStatus(ProductStatus::STATUS_SELL);
        $order->product->booked_at = null;

        $order->product->save();

        DB::commit();

        $eventClass = $cancelInitiator->getEventClass();

        if (!$eventClass) {
            return;
        }

        $this->dispatcher->dispatch(new $eventClass($order));
    }

    private function check(Order $order, CancelInitiator $cancelInitiator, bool $reservationPartiallyPaidOrders): void
    {
        if (!in_array($order->status->getValue(), [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID], true)) {
            throw new CantCancelOrderException();
        }

        if (
            $cancelInitiator->getValue() === CancelInitiator::INITIATOR_TIMEOUT
            && $order->status->getValue() === OrderStatus::STATUS_PARTIALLY_PAID
            && !$reservationPartiallyPaidOrders
        ) {
            throw new CantCancelOrderException();
        }

        if (
            $cancelInitiator->getValue() === CancelInitiator::INITIATOR_CLIENT
            && $order->status->getValue() !== OrderStatus::STATUS_AWAITING_PAYMENT
        ) {
            throw new CantCancelOrderException();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Models\SellerSetting;
use App\Services\Order\Exceptions\CantCancelOrderException;
use App\Services\Order\VO\CancelInitiator;
use App\Services\Order\VO\OrderStatus;
use Illuminate\Support\Collection;

/**
 * Class CheckOrdersTimeoutCommand.
 */
final class CheckOrdersTimeoutCommand
{
    private CancelOrderCommand $cancelOrderCommand;

    public function __construct(CancelOrderCommand $cancelOrderCommand)
    {
        $this->cancelOrderCommand = $cancelOrderCommand;
    }

    public function execute(): void
    {
        Order::whereIn('status', [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID])
            ->with('product', 'seller.settings')
            ->chunk(
                100,
                function (Collection $orders) {
                    $orders->each(function (Order $order) {
                        /** @var SellerSetting $reservationPartiallyPaidOrders */
                        $reservationPartiallyPaidOrders = $order->seller->settings
                            ->where('key', 'auto_cancel_partially_paid_orders')
                            ->first();
                        if ($order->status->getValue() === OrderStatus::STATUS_PARTIALLY_PAID
                            && (!$reservationPartiallyPaidOrders->value)) {
                            return;
                        }

                        $reservationTime = $order->seller->settings
                            ->where('key', $order->payment_method->getReservationTimeKey())
                            ->first();

                        if ($order->product->booked_at->addMinutes((int) $reservationTime->value)->isPast()) {
                            try {
                                $this->cancelOrderCommand->execute(
                                    $order,
                                    new CancelInitiator(CancelInitiator::INITIATOR_TIMEOUT),
                                    (bool) $reservationPartiallyPaidOrders->value
                                );
                            } catch (CantCancelOrderException $e) {
                            }
                        }
                    });
                }
            );
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Services\Order\VO\OrderStatus;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;
use Throwable;

/**
 * Class SetStatusOperatorCommand.
 */
final class SetStatusOperatorCommand
{
    /**
     * @var OrderPaidHandler
     */
    private OrderPaidHandler $orderPaidHandler;

    /**
     * SetStatusOperatorCommand constructor.
     *
     * @param OrderPaidHandler $orderPaidHandler
     */
    public function __construct(OrderPaidHandler $orderPaidHandler)
    {
        $this->orderPaidHandler = $orderPaidHandler;
    }

    /**
     * @param Order      $order
     * @param int        $status
     * @param float|null $price
     *
     * @throws Throwable
     *
     * @return Order
     */
    public function execute(Order $order, int $status, ?float $price = null): Order
    {
//        if ($order->product->productType->delivery_type->isTaxi()) {
//            return $order;
//        }

        DB::beginTransaction();

        $amount = new Money(($price ?: 0) * 100, new Currency('UAH'));

        if ($status === OrderStatus::STATUS_GIVEN && $price > 0) {
            $this->setGiftStatus($order, $amount);
        }

        $this->orderPaidHandler->handle($order, $status, $amount);

        DB::commit();

        DB::rollBack();

        return $order;
    }

    /**
     * @param Order $order
     * @param Money $price
     */
    private function setGiftStatus(Order $order, Money $price): void
    {
        $wallet = $order->wallet;

        $wallet->createPaymentForGiven($order, $price);
    }
}

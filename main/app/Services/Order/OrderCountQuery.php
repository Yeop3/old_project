<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Services\Order\VO\OrderStatus;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderCountQuery.
 */
class OrderCountQuery
{
    /**
     * @param int      $sellerId
     * @param int|null $clientId
     *
     * @return int[]
     */
    public function execute(int $sellerId, int $clientId): array
    {
        $map = [
            OrderStatus::STATUS_AWAITING_PAYMENT           => 'awaiting_payment',
            OrderStatus::STATUS_PARTIALLY_PAID             => 'partially_paid',
            OrderStatus::STATUS_PAID                       => 'paid',
            OrderStatus::STATUS_CANCELED_BY_CLIENT         => 'canceled_by_client',
            OrderStatus::STATUS_CANCELED_BY_OPERATOR       => 'canceled_by_operator',
            OrderStatus::STATUS_CANCELED_BY_TIMEOUT        => 'canceled_by_timeout',
            OrderStatus::STATUS_CANCELED_BY_SYSTEM         => 'canceled_by_system',
            OrderStatus::STATUS_GIVEN                      => 'given_operator',
            OrderStatus::STATUS_IN_DELIVERY                => 'in_delivery',
            OrderStatus::STATUS_DELIVERED                  => 'delivered',
        ];

        $count = array_fill_keys($map, 0);

        $orderStatusCounts = DB::table('orders')
            ->where('orders.seller_id', $sellerId)
            ->where('orders.client_id', $clientId)
            ->select('orders.status', DB::raw('count(orders.status) as total'))
            ->groupBy('orders.status')
            ->get();

        foreach ($orderStatusCounts as $orderStatusCount) {
            if (array_key_exists($orderStatusCount->status, $map)) {
                $count[$map[$orderStatusCount->status]] = $orderStatusCount->total;
            }
        }

        return $count;
    }
}

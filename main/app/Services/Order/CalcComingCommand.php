<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CalcComingCommand.
 */
final class CalcComingCommand
{
    public function execute(): array
    {
        /* @var User $user */
        $user = auth()->user();
        $orders = Order::whereSellerId($user->seller_id)
            ->with('client', 'shift')
            ->whereNull('canceled_at')
            ->whereHas('shift', static function (Builder $builder) {
                $builder->current();
            })
            ->get();

        $negotiateOrders = Order::whereSellerId($user->seller_id)
            ->with('shift')
            ->whereIn('status', [8, 2, 1])
            ->whereHas('shift', static function (Builder $builder) {
                $builder->current();
            })
            ->get();

        return [
            'coming'              => $orders->map(
                fn (Order $order) => formatMoney($order->total_price->subtract($order->unpaid_amount))
            )->sum(),

            'consumption' => -1 * abs(
                $negotiateOrders->map(
                    fn (
                        Order $order
                    ) => formatMoney($order->unpaid_amount)
                )->sum()
            ),
        ];
    }
}

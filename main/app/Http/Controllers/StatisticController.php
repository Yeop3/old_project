<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Order;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use App\Services\Statistic\StatisticQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class StatisticController.
 */
class StatisticController extends Controller
{
    /**
     * @param StatisticQuery $query
     *
     * @return array
     */
    public function index(StatisticQuery $query): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $query->execute($user->seller_id);
    }

    /**
     * @param StatisticQuery $query
     * @param $status
     *
     * @return array
     */
    public function show(StatisticQuery $query, $status): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $query->execute($user->seller_id, new ProductStatus($status));
    }

    /**
     * @param Request $request
     *
     * @return array|Collection
     */
    public function chart(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $showDays = parseBool($request->get('show_days'));

        $statistic = DB::table('products')
            ->select([
                DB::raw('count(products.id) as count'),
                DB::raw('YEAR(orders.completed_at) as year'),
                DB::raw('MONTH(orders.completed_at) as month'),
            ])
            ->when(
                $showDays,
                fn (Builder $builder) => $builder->addSelect(DB::raw('DAY(orders.completed_at) as day'))
            )
            ->join('orders', function ($join) {
                $join->on('products.id', '=', 'orders.product_id')
                    ->whereIn('orders.status', [OrderStatus::STATUS_PAID, OrderStatus::STATUS_GIVEN]);
            })
            ->where('products.seller_id', $user->seller_id)
            ->where('products.status', ProductStatus::STATUS_SOLD);

        if ($request->get('driver')) {
            $driver = Driver::whereSellerId($user->seller_id)->whereNumber($request->get('driver'))->first();

            if ($driver) {
                $statistic->where('products.driver_id', $driver->id);
            }
        }

        if ($request->get('location')) {
            $location = Location::whereSellerId($user->seller_id)->whereNumber($request->get('location'))->first();

            if ($location) {
                $statistic->where('products.location_id', $location->id);
            }
        }

        if ($request->get('product_type')) {
            $productType = ProductType::whereSellerId($user->seller_id)->whereNumber($request->get('product_type'))->first();

            if ($productType) {
                $statistic->where('products.product_type_id', $productType->id);
            }
        }

        if ($request->get('bot')) {
            $bot = Bot::whereSellerId($user->seller_id)->whereNumber($request->get('bot'))->first();

            if ($bot) {
                $statistic->where('orders.source_id', $bot->id);
            }
        }

        $dateStart = carbonSafeParse($request->get('date_start'));
        $dateEnd = carbonSafeParse($request->get('date_end'));

        if ($dateStart && $dateEnd) {
            $statistic->whereBetween(
                'orders.completed_at',
                [$request->get('date_start'), $request->get('date_end')]
            );
        } elseif ($dateStart && !$dateEnd) {
            $statistic->whereDate('orders.completed_at', '>=', $dateStart);
        } elseif (!$dateStart && $dateEnd) {
            $statistic->whereDate('orders.completed_at', '<', $dateEnd);
        }

        $statistic
            ->groupBy(['year'])
            ->groupBy(['month'])
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->when(
                $showDays,
                fn (Builder $builder) => $builder
                    ->groupBy(['day'])
                    ->orderBy('day', 'ASC')
            );

        $statistic = $statistic->get();

        if (!$dateStart) {
            $dateStart = Order::orderBy('completed_at')->first()->completed_at ?? null;
        }

        if (!$dateStart) {
            return $statistic;
        }

        if (!$dateEnd) {
            $dateEnd = now();
        }

        $iterateFunc = $showDays ? 'addDay' : 'addMonth';

        $getDateKey = fn ($item) => $showDays
            ? "$item->day.$item->month.$item->year"
            : "$item->month.$item->year";

        $statisticGrouped = $statistic->keyBy($getDateKey);

        $items = [];

        for ($date = $dateStart; $date < $dateEnd; $date->{$iterateFunc}()) {
            if ($statisticGrouped[$getDateKey($date)] ?? false) {
                $items[] = $statisticGrouped[$getDateKey($date)];

                continue;
            }

            $items[] = [
                'count' => 0,
                'day'   => $date->day,
                'month' => $date->month,
                'year'  => $date->year,
            ];
        }

        return $items;
    }
}

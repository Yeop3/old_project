<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;

/**
 * Class SellerStatisticController.
 */
class SellerStatisticController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function __invoke(Request $request)
    {
        $dateStart = carbonSafeParse($request->get('date_start'));
        $dateEnd = carbonSafeParse($request->get('date_end'));

        if (is_array($request->get('statuses'))) {
            $statuses = $request->get('statuses');
        } else {
            $statuses = [$request->get('statuses')];
        }

        $id = $request->get('seller_id');

        $ordersCountsByStatus = $this->getOrdersCounts($id, $dateStart, $dateEnd);

        $ordersTotalAmount = $this->getTotalSum($id, $dateStart, $dateEnd, $statuses);
        $ordersTotalSum = formatMoney($ordersTotalAmount);

        $ordersUnpaidAmount = $this->getUnpaidSum($id, $dateStart, $dateEnd, $statuses);
        $ordersUnpaidSum = formatMoney($ordersUnpaidAmount);

        $ordersPaidAmount = $ordersTotalAmount->subtract($ordersUnpaidAmount);
        $ordersPaidSum = formatMoney($ordersPaidAmount);

        return [
            'statuses_counts' => $ordersCountsByStatus,
            'total_sum'       => $ordersTotalSum,
            'paid_sum'        => $ordersPaidSum,
            'unpaid_sum'      => $ordersUnpaidSum,
        ];
    }

    /**
     * @param int|null    $id
     * @param Carbon|null $dateStart
     * @param Carbon|null $dateEnd
     *
     * @return mixed
     */
    private function getOrdersCounts(?int $id, ?Carbon $dateStart, ?Carbon $dateEnd)
    {
        return Order::select(['status', DB::raw('count(*) as total')])
            ->when($id, fn (Builder $builder) => $builder->where('seller_id', $id))
            ->dateRange($dateStart, $dateEnd)
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * @param int|null    $id
     * @param Carbon|null $dateStart
     * @param Carbon|null $dateEnd
     * @param $statuses
     *
     * @return Money
     */
    private function getTotalSum(?int $id, ?Carbon $dateStart, ?Carbon $dateEnd, $statuses): Money
    {
        $ordersTotalSumInt = Order::when($id, fn (Builder $builder) => $builder->where('seller_id', $id))
            ->dateRange($dateStart, $dateEnd)
            ->inStatuses($statuses)
            ->sum('total_price');

        return new Money($ordersTotalSumInt, new Currency('UAH'));
    }

    /**
     * @param int|null    $id
     * @param Carbon|null $dateStart
     * @param Carbon|null $dateEnd
     * @param $statuses
     *
     * @return Money
     */
    private function getUnpaidSum(?int $id, ?Carbon $dateStart, ?Carbon $dateEnd, $statuses): Money
    {
        $ordersUnpaidSumInt = Order::when($id, fn (Builder $builder) => $builder->where('seller_id', $id))
            ->dateRange($dateStart, $dateEnd)
            ->inStatuses($statuses)
            ->sum('unpaid_amount');

        return new Money($ordersUnpaidSumInt, new Currency('UAH'));
    }
}

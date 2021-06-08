<?php

namespace App\Http\Controllers;

use App\Exports\ShiftExport;
use App\Models\Operator;
use App\Models\ProductType;
use App\Models\Shift;
use App\Models\User;
use App\Services\Order\OrderCountQuery;
use App\Services\Order\VO\OrderStatus;
use App\Services\Shift\StartNewShiftCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ShiftController.
 */
class ShiftController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $shifts = Shift::with('operator')->whereSellerId($user->seller_id)
            ->numberFilter($request->get('number'))
            ->operatorFilter($request->get('operator'))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'));

        if ($operator = $request->get('operators')) {
            $operatro = Operator::whereSellerId($user->seller_id)->where('id', $operator)->first();
            if ($operatro) {
                $shifts->where('operator_id', $operatro->id);
            }
        }

        return $shifts->get();
    }

    /**
     * @return Shift|Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function current()
    {
        /** @var User $user */
        $user = auth()->user();

        return Shift::whereSellerId($user->seller_id)->whereNull('ended_at')->with('operator')->first();
    }

    /**
     * @param StartNewShiftCommand $command
     * @param $operatorNumber
     *
     * @return Shift
     */
    public function startNew(StartNewShiftCommand $command, $operatorNumber): Shift
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        return $command->execute($user->seller, (int) $operatorNumber)->load('operator');
    }

    /**
     * @param OrderCountQuery $query
     * @param $id
     *
     * @return array
     */
    public function show(OrderCountQuery $query, $id): array
    {
        /** @var User $user */
        $user = auth()->user();

        $shift = Shift::whereSellerId($user->seller_id)
            ->whereNumber($id)
            ->with([
                'operator',
                'orders', ])
            ->firstOrFail();

        $count = $query->execute($user->seller_id, null, $shift->id);

        $productTypes = ProductType::whereSellerId($user->seller_id)
            ->with(['orders' => function ($query) use ($id) {
                return $query->with('shift')->whereHas('shift', function ($query) use ($id) {
                    return $query->where('number', $id);
                });
            }])
            ->whereHas(
                'products',
                fn (Builder $q) => $q
                ->whereHas('orders', fn (Builder $q) => $q
                    ->where('shift_id', $id))
            )
            ->withCount(['orders as paid' => function ($query) {
                $query->where('orders.status', '=', OrderStatus::STATUS_PAID);
            }, 'orders as given_operator' => function ($query) {
                $query->where('orders.status', '=', OrderStatus::STATUS_GIVEN);
            }, 'orders as relocation' => function ($query) {
                return 0;
            }])
            ->get();

        return [
            'productInfo' => $productTypes,
            'shiftInfo'   => $shift,
            'ordersCount' => $count,
        ];
    }

    public function exportById(string $id): BinaryFileResponse
    {
        /* @var User $user */
        $user = auth()->user();

        return Excel::download(new ShiftExport($user->seller, (int) $id), 'test.xlsx');
    }

    public function exportByCurrent(): BinaryFileResponse
    {
        /* @var User $user */
        $user = auth()->user();
        $shiftId = Shift::whereSellerId($user->seller_id)
            ->current()
            ->firstOrFail()
            ->id;

        return Excel::download(new ShiftExport($user->seller, $shiftId), 'test.xlsx');
    }

    public function exportByPrev(): BinaryFileResponse
    {
        /* @var User $user */
        $user = auth()->user();
        $shiftId = Shift::whereSellerId($user->seller_id)
            ->whereNotNull('ended_at')
            ->latest('id')
            ->firstOrFail()
            ->id;

        return Excel::download(new ShiftExport($user->seller, $shiftId), 'test.xlsx');
    }
}

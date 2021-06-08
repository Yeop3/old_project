<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\SetStatusGiveRequest;
use App\Models\Bot;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use App\Services\Order\CalcComingCommand;
use App\Services\Order\CancelOrderCommand;
use App\Services\Order\RestorationCanceledOrderCommand;
use App\Services\Order\RestorationPaidOrderCommand;
use App\Services\Order\SetStatusOperatorCommand;
use App\Services\Order\VO\CancelInitiator;
use App\Services\Order\VO\OrderStatus;
use App\VO\SourceType;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use JsonException;
use Throwable;
use Tightenco\Collect\Support\Collection;

/**
 * Class OrderController.
 */
class OrderController extends Controller
{
    /**
     * @return Order[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function forSelect()
    {
        /* @var User $user */
        $user = auth()->user();
        $type = \request('is_all');
        $order = Order::whereSellerId($user->seller_id)
            ->when(!$type, fn (Builder $builder) => $builder
                ->whereIn(
                    'status',
                    [
                        OrderStatus::STATUS_AWAITING_PAYMENT,
                        OrderStatus::STATUS_PARTIALLY_PAID,
                    ]
                ))
            ->with(['product', 'product.productType'])
            ->get();

        return $order->map(fn (Order $order) => [
            'value' => $order->number,
            'text'  => $order->getFullName(),
        ]);
    }

    public function getStatus(): array
    {
        return OrderStatus::STATUSES;
    }

    /**
     * @return Seller[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSellers()
    {
        return Seller::all();
    }

    public function index(Request $request): LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();

        /** @var LengthAwarePaginator $orders */
        $orders = Order::with([
            'client',
            'shift.operator',
            'product' => function ($query) {
                $query->withTrashed()->get();
            },
            'product.location.ancestors',
            'product.productType.discounts',
            'source',
            'wallet',
            'discount',
            'seller',
        ])
            ->paymentMethodFilter($request->get('payment_method'))
            ->productTypeFilter($request->get('product_type'))
            ->productNameFilter($request->get('product_name'))
            ->clientNameFilter($request->get('client_name'))
            ->dateFilter($request->get('created_at'))
            ->orderFilter($request->get('order'))
            ->orderStatus($request->get('order_status'))
            ->numberFilter($request->get('number'))
            ->sellerFilter($request->get('order_sellers'))
            ->ofSeller($user, parseIntFromInput($request->get('seller_id')))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'))
            ->when(
                $bot_number = $request->get('bot_number'),
                fn (Builder $builder) => $builder
                    ->where('source_type', SourceType::TYPE_BOT)
                    ->whereHasMorph(
                        'source',
                        [Bot::class],
                        fn (Builder $q) => $q
                            ->where('number', $bot_number)
                    )
            )
            ->paginate(20);

        $transactions = $this->getOrdersTransactions($orders);

        $orders->getCollection()->each(function (Order $order) use ($transactions) {
            $order->append([
                'discount_value',
                'delivery_type',
                'delivery_type_readable',
                'is_for_delivery',
                'readable_unit',
            ]);

            if ($order->product) {
                $order->product->location->append(['name_chain']);
            }

            $order->transactions = $transactions
                ->filter(fn ($t) => (int) ($t->order_id) === (int) $order->id && $t->created_at >= $order->created_at)
                ->values();

            return $order;
        });

        return $orders;
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return Order[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show(Request $request, $id)
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::ofSeller($user, parseIntFromInput($request->get('seller_id')))
            ->where('number', $id)
            ->with([
                'client',
                'shift',
                'shift.operator',
                'product'             => function ($query) {
                    $query->withTrashed()->get();
                },
                'product.location',
                'product.location.ancestors',
                'product.productType' => function ($query) {
                    $query->withTrashed()->get();
                },
                'product.productType.discounts',
                'wallet',
                'source',
                'discount',
            ])->get();

        $transactions = $this->getOrdersTransactions($order);

        $order->each(function (Order $order) use ($transactions) {
            $order->append([
                'discount_value',
                'delivery_type',
                'delivery_type_readable',
                'is_for_delivery',
                'readable_unit',
            ]);

            if ($order->product) {
                $order->product->location->append(['name_chain']);
            }

            $order->transactions = $transactions
                ->filter(fn ($t) => (int) ($t->order_id) === (int) $order->id && $t->created_at >= $order->created_at)
                ->values();

            return $order;
        });

        return $order;
    }

    /**
     * @return JsonResponse
     */
    public function getCountToFilter(): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();

        return response()->json([
            'all'           => Order::whereSellerId($user->seller_id)->count(),
            'shift_current' => Order::whereSellerId($user->seller_id)
                ->orderFilter('shift_current')
                ->count(),
            'shift_prev'    => Order::whereSellerId($user->seller_id)
                ->orderFilter('shift_prev')
                ->count(),
        ]);
    }

    public function getOrderStatusCounter(Request $request): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();

        $counterArray = (['order_status' => []]);

        $counterArray['order_status'] = Order::ofSeller($user, parseIntFromInput($request->get('seller_id')))
            ->orderFilter($request->get('order'))
            ->getQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json($counterArray);
    }

    /**
     * @param SetStatusGiveRequest     $request
     * @param SetStatusOperatorCommand $command
     * @param $id
     *
     * @throws Throwable
     *
     * @return Order
     */
    public function setStatusGive(SetStatusGiveRequest $request, SetStatusOperatorCommand $command, $id): Order
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)->whereNumber($id)->firstOrFail();

        return $command->execute(
            $order,
            OrderStatus::STATUS_GIVEN,
            parseFloatFromInput($request->get('price'))
        );
    }

    /**
     * @param SetStatusOperatorCommand $command
     * @param $id
     *
     * @throws Throwable
     *
     * @return Order
     */
    public function setStatusTransfer(SetStatusOperatorCommand $command, $id): Order
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)->whereNumber($id)->firstOrFail();

        return $command->execute($order, OrderStatus::STATUS_GIVEN);
    }

    /**
     * @param CancelOrderCommand $command
     * @param $id
     *
     * @return Order
     */
    public function setCanceledByOperator(CancelOrderCommand $command, $id): Order
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)->whereNumber($id)->firstOrFail();

        $command->execute($order, new CancelInitiator(CancelInitiator::INITIATOR_OPERATOR));

        return $order->refresh();
    }

    public function cancelAll(CancelOrderCommand $command): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)
            ->whereIn('status', [
                OrderStatus::STATUS_AWAITING_PAYMENT,
                OrderStatus::STATUS_PARTIALLY_PAID,
            ])
            ->get();
        $order->map(
            fn (Order $order) => $command->execute(
                $order,
                new CancelInitiator(
                    CancelInitiator::INITIATOR_OPERATOR
                )
            )
        );

        return response()->json(['message' => 'done']);
    }

    public function cancelAwaiting(CancelOrderCommand $command): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)
            ->where('status', OrderStatus::STATUS_AWAITING_PAYMENT)
            ->get();

        $order->map(fn (Order $order) => $command->execute(
            $order,
            new CancelInitiator(CancelInitiator::INITIATOR_OPERATOR)
        ));

        return response()->json(['message' => 'done']);
    }

    /**
     * @param CancelOrderCommand $command
     *
     * @return JsonResponse
     */
    public function cancelPartially(CancelOrderCommand $command): JsonResponse
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)
            ->where('status', OrderStatus::STATUS_PARTIALLY_PAID)
            ->get();

        $order->map(fn (Order $order) => $command->execute(
            $order,
            new CancelInitiator(CancelInitiator::INITIATOR_OPERATOR)
        ));

        return response()->json(['message' => 'done']);
    }

    /**
     * @param CalcComingCommand $command
     *
     * @return JsonResponse
     */
    public function calcComing(CalcComingCommand $command): JsonResponse
    {
        return response()->json($command->execute());
    }

    /**
     * @return mixed
     */
    public function getCrypto()
    {
        return Cache::get('crypto_price');
    }

    /**
     * @param $orders
     *
     * @return \Illuminate\Support\Collection|Collection
     */
    public function getOrdersTransactions($orders)
    {
        $groupOrders = $orders->groupBy('wallet_type');
        $orderIds = $orders->pluck('id');
        $transactionModels = collect();

        foreach ($groupOrders as $models) {
            $modelWithWallet = $models->filter(fn ($model) => $model->wallet !== null)->first();

            if (!$modelWithWallet) {
                continue;
            }

            $wallet = $modelWithWallet->wallet;

            $relationName = $wallet->getWalletType()->getTransactionRelationName();

            if ($relationName) {
                $transactionModels->push($wallet->{$relationName}()->whereIn('order_id', $orderIds)->get());
            }
        }

        return $transactionModels->collapse();
    }

    public function restorationCanceledOrder(RestorationCanceledOrderCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)
            ->whereIn('status', [
                OrderStatus::STATUS_CANCELED_BY_CLIENT,
                OrderStatus::STATUS_CANCELED_BY_OPERATOR,
                OrderStatus::STATUS_CANCELED_BY_TIMEOUT,
                OrderStatus::STATUS_CANCELED_BY_SYSTEM,
            ])
            ->whereNumber(\request('number'))
            ->firstOrFail();

        $product = $order->product_maybe_deleted;

        $command->execute($user->seller_id, $product, $order);
    }

    /**
     * @param RestorationPaidOrderCommand $command
     *
     * @throws JsonException
     */
    public function restorationPaidOrder(RestorationPaidOrderCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();

        $order = Order::whereSellerId($user->seller_id)
            ->whereIn('status', [OrderStatus::STATUS_PAID, OrderStatus::STATUS_GIVEN])
            ->whereNumber(\request('number'))
            ->firstOrFail();

        $product = $order->product_maybe_deleted;

        $command->execute($user->seller_id, $product, $order);
    }
}

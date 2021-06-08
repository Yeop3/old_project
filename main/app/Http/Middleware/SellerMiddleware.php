<?php

namespace App\Http\Middleware;

use App\Models\Shift;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SellerMiddleware.
 */
class SellerMiddleware
{
    private const CLEAR_KEYS = [
        'id',
        'parent_id',
        'seller_id',
        'operator_id',
        'driver_id',
        'product_type_id',
        'location_id',
        'product_id',
        'shift_id',
        'client_id',
        'logic_id',
        'discount_id',
        'order_id',
        'proxy_id',
        'password',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            abort(401);
        }

        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        $host = $request->getHost();

        $sellerHost = $user->seller->domain;

        if ($user->seller->banned === 1) {
            abort(401);
        }

        if ($host !== $sellerHost) {
            abort(404);
        }

        if ($user->role !== User::ROLE_SELLER && $user->role !== User::ROLE_OPERATOR) {
            abort(403);
        }

        if ($user->role === User::ROLE_OPERATOR) {
            $operator = $user->operator;
            $shift = Shift::current()
                ->where('seller_id', $user->seller_id)
                ->first();
            if ($shift->operator->id !== $operator->id) {
                abort(403, 'Сейчас не ваша смена');
            }
        }

        /** @var JsonResponse|Response $response */
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->getStatusCode() !== 422) {
            $response->setData(
                clearArray($response->getData(true), self::CLEAR_KEYS)
            );
        }

        return $response;
    }
}

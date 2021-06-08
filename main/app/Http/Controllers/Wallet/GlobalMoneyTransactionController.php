<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet\GlobalMoneyTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class GlobalMoneyTransactionController.
 */
class GlobalMoneyTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();
        $wallet = \request('wallet');
        $order = \request('order');

        return GlobalMoneyTransaction::whereSellerId($user->seller_id)
            ->with([
                'order',
                'globalMoneyWallet',
            ])
            ->when($wallet, fn (Builder $builder) => $builder
                ->whereHas('globalMoneyWallet', fn (Builder $builder) => $builder
                    ->where('number', $wallet)))
            ->when($order, fn (Builder $builder) => $builder
                ->whereHas('order', fn (Builder $builder) => $builder
                    ->where('number', $order)))
            ->paginate(20);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return GlobalMoneyTransaction
     */
    public function show($id): GlobalMoneyTransaction
    {
        /* @var User $user */
        $user = auth()->user();

        return GlobalMoneyTransaction::whereSellerId($user->seller_id)
            ->with([
                'order',
                'globalMoneyWallet',
            ])
            ->where('number', $id)
            ->firstOrFail();
    }
}

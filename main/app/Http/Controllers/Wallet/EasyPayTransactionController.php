<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet\EasyPayTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * Class EasyPayTransactionController.
 */
class EasyPayTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();
        $wallet = \request('wallet');
        $order = \request('order');

        return EasyPayTransaction::whereSellerId($user->seller_id)
            ->with([
                'order',
                'easyPayWallet',
            ])
            ->when($wallet, fn (Builder $builder) => $builder
                ->whereHas('easyPayWallet', fn (Builder $builder) => $builder
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
     * @return EasyPayTransaction|Builder|Model|Response
     */
    public function show($id)
    {
        /* @var User $user */
        $user = auth()->user();

        return EasyPayTransaction::whereSellerId($user->seller_id)
            ->with([
                'order',
                'easyPayWallet',
            ])
            ->where('number', $id)
            ->firstOrFail();
    }
}

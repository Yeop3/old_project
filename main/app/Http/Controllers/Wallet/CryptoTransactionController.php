<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet\CryptoTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class CryptoTransactionController.
 */
class CryptoTransactionController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return CryptoTransaction::whereSellerId($user->seller->id)
            ->with(['order', 'cryptoWallet'])
            ->when(\request('number'), fn (Builder $builder) => $builder
                ->where('number', request('number')))
            ->when(\request('order'), fn (Builder $builder) => $builder
                ->whereHas('order', fn (Builder $builder) => $builder
                    ->where('number', \request('order'))))
            ->when(\request('address'), fn (Builder $builder) => $builder
                ->where('address', 'like', '%'.\request('address').'%'))
            ->when(\request('crypto_wallet'), fn (Builder $builder) => $builder
                ->whereHas('cryptoWallet', fn (Builder $builder) => $builder
                    ->where('number', \request('crypto_wallet'))))
            ->paginate(20);
    }
}

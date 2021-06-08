<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet\KunaCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class KunaCodeController.
 */
class KunaCodeController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();
        $code = request('code');

        return KunaCode::whereSellerId($user->seller_id)
            ->when($code, fn (Builder $builder) => $builder->where('code', 'like', "%$code%"))
            ->with(['kunaAccount:id,name,number', 'shift:id,number', 'order:id,number'])
            ->paginate(20);
    }

    /**
     * @param $id
     *
     * @return KunaCode|Builder|Model
     */
    public function show($id)
    {
        /* @var User $user */
        $user = auth()->user();

        return KunaCode::whereSellerId($user->seller_id)->where('number', $id)
                ->with(['kunaAccount:id,name', 'shift:id,number', 'order:id,number'])
                ->firstOrFail();
    }
}

<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\Kuna\KunaWalletCreateRequest;
use App\Http\Requests\Wallet\Kuna\KunaWalletUpdateRequest;
use App\Models\User;
use App\Models\Wallet\KunaAccount;
use App\Services\Wallet\Kuna\Create\KunaWalletCreateCommand;
use App\Services\Wallet\Kuna\Delete\KunaWalletDeleteCommand;
use App\Services\Wallet\Kuna\Update\KunaWalletUpdateCommand;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KunaWalletController.
 */
class KunaWalletController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();
        $name = request('name');
        $comment = request('comment');
        $active = request('active');

        return KunaAccount::whereSellerId($user->seller_id)
            ->when($name, fn (Builder $builder) => $builder->where('name', 'like', "%$name%"))
            ->when($comment, fn (Builder $builder) => $builder->where('comment', 'like', "%$comment%"))
            ->when($active !== null, fn (Builder $builder) => $builder->whereActive($active))
            ->with('proxy')
            ->paginate(20);
    }

    public function store(KunaWalletCreateRequest $request, KunaWalletCreateCommand $command): KunaAccount
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), $user->seller);
    }

    /**
     * @param $id
     *
     * @return KunaAccount|Builder|Model
     */
    public function show($id)
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return KunaAccount::whereSellerId($user->seller_id)->where('number', $id)->with('proxy')->firstOrFail();
    }

    /**
     * @param KunaWalletUpdateRequest $request
     * @param KunaWalletUpdateCommand $command
     * @param $id
     *
     * @return KunaAccount
     */
    public function update(KunaWalletUpdateRequest $request, KunaWalletUpdateCommand $command, $id): KunaAccount
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), (int) $id, $user->seller);
    }

    /**
     * @param KunaWalletDeleteCommand $command
     * @param $id
     *
     * @throws Exception
     */
    public function destroy(KunaWalletDeleteCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute((int) $id, $user->seller);
    }
}

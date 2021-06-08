<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crypto\CryptoWalletCreateRequest;
use App\Http\Requests\Crypto\CryptoWalletUpdateRequest;
use App\Models\User;
use App\Models\Wallet\CryptoWallet;
use App\Services\Wallet\Crypto\Create\CryptoWalletCreateCommand;
use App\Services\Wallet\Crypto\Delete\CryptoWalletDeleteCommand;
use App\Services\Wallet\Crypto\Update\CryptoWalletUpdateCommand;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CryptoWalletController.
 */
class CryptoWalletController extends Controller
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

        return CryptoWallet::whereSellerId($user->seller_id)
            ->when($name, fn (Builder $builder) => $builder->where('name', 'like', "%$name%"))
            ->when($comment, fn (Builder $builder) => $builder->where('comment', 'like', "%$comment%"))
            ->with('proxy')
            ->paginate(20);
    }

    public function store(CryptoWalletCreateRequest $request, CryptoWalletCreateCommand $command): CryptoWallet
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), $user->seller);
    }

    /**
     * @param CryptoWalletDeleteCommand $command
     * @param $id
     *
     * @throws Exception
     */
    public function destroy(CryptoWalletDeleteCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param $id
     *
     * @return CryptoWallet|Builder|Model
     */
    public function show($id)
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return CryptoWallet::whereSellerId($user->seller_id)->where('number', $id)->with('proxy')->firstOrFail();
    }

    /**
     * @param CryptoWalletUpdateRequest $request
     * @param CryptoWalletUpdateCommand $command
     * @param $id
     *
     * @return CryptoWallet
     */
    public function update(CryptoWalletUpdateRequest $request, CryptoWalletUpdateCommand $command, $id): CryptoWallet
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), (int) $id, $user->seller);
    }

    /**
     * @return CryptoWallet[]|Builder[]|Collection
     */
    public function getSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return CryptoWallet::whereSellerId($user->seller->id)->get(['name as text', 'number as value']);
    }
}

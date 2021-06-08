<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\GlobalMoney\GlobalMoneyWalletCreateRequest;
use App\Http\Requests\Wallet\GlobalMoney\GlobalMoneyWalletEditRequest;
use App\Models\User;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Services\Wallet\GlobalMoney\Create\GlobalMoneyWalletCreateCommand;
use App\Services\Wallet\GlobalMoney\Delete\GlobalMoneyWalletDeleteCommand;
use App\Services\Wallet\GlobalMoney\Edit\GlobalMoneyWalletEditCommand;
use App\Services\Wallet\GlobalMoney\GlobalMoneyApi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class GlobalMoneyWalletController.
 */
class GlobalMoneyWalletController extends Controller
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
        $name = \request('name');

        return GlobalMoneyWallet::whereSellerId($user->seller_id)
            ->with('proxy')
            ->when($name, fn (Builder $builder) => $builder->where('name', 'like', "%$name%"))
            ->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GlobalMoneyWalletCreateRequest $request
     * @param GlobalMoneyWalletCreateCommand $command
     *
     * @return GlobalMoneyWallet
     */
    public function store(GlobalMoneyWalletCreateRequest $request, GlobalMoneyWalletCreateCommand $command): GlobalMoneyWallet
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), $user->seller);
    }

    /**
     * @param string $id
     *
     * @return GlobalMoneyWallet|Builder|Model
     */
    public function show(string $id)
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return GlobalMoneyWallet::whereSellerId($user->seller_id)
            ->where('number', $id)
            ->with('proxy')
            ->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GlobalMoneyWalletEditRequest $request
     * @param GlobalMoneyWalletEditCommand $command
     * @param string                       $id
     *
     * @return GlobalMoneyWallet
     */
    public function update(GlobalMoneyWalletEditRequest $request, GlobalMoneyWalletEditCommand $command, string $id): GlobalMoneyWallet
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), $user->seller, (int) $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param GlobalMoneyWalletDeleteCommand $command
     * @param string                         $id
     *
     * @return void
     */
    public function destroy(GlobalMoneyWalletDeleteCommand $command, string $id): void
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($id, $user->seller);
    }

    /**
     * @return GlobalMoneyWallet[]|Builder[]|Collection
     */
    public function getSelect()
    {
        /* @var User $user */
        $user = auth()->user();

        return GlobalMoneyWallet::whereSellerId($user->seller_id)
            ->get(['name as text', 'number as value']);
    }

    /**
     * @param Request        $request
     * @param GlobalMoneyApi $api
     */
    public function checkAccess(Request $request, GlobalMoneyApi $api): void
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $globalMoneyWallet = GlobalMoneyWallet::whereSellerId($user->seller_id)->where('login', $request->get('login'))->firstOrFail();

        $api->setLoginData($globalMoneyWallet->login_data);
        $api->login();
        $wallets = GlobalMoneyWallet::whereLogin($request->get('login'))->where('password', $request->get('password'))->get();

        foreach ($wallets as $wallet) {
            $wallet->wrong_credentials = 0;
            $wallet->save();
        }
    }
}

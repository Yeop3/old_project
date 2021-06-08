<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\EasyPay\EasyPayCreateRequest;
use App\Http\Requests\Wallet\EasyPay\EasyPayEditRequest;
use App\Models\User;
use App\Models\Wallet\EasyPayWallet;
use App\Services\Wallet\EasyPayWallet\CheckAccountLoginCommand;
use App\Services\Wallet\EasyPayWallet\Create\EasyPayWalletCreateCommand;
use App\Services\Wallet\EasyPayWallet\Delete\EasyPayWalletDeleteCommand;
use App\Services\Wallet\EasyPayWallet\Edit\EasyPayWalletEditCommand;
use App\Services\Wallet\EasyPayWallet\RestoreBalanceCommand;
use danog\MadelineProto\auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use function request;

/**
 * Class EasyPayWalletController.
 */
class EasyPayWalletController extends Controller
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
        $name = request('name');
        $easyPays = EasyPayWallet::whereSellerId($user->seller_id)
            ->with([
                'transactions.order',
                'proxy',
            ])
            ->when($name, fn (Builder $builder) => $builder->where('name', 'like', "%$name%"))
            ->paginate(20);
        $easyPays
            ->getCollection()
            ->each(function (EasyPayWallet $easyPayWallet) {
                $easyPayWallet->append(['balance', 'is_limit']);
                $easyPayWallet->setHidden(['transactions']);

                return $easyPayWallet;
            });

        return $easyPays;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EasyPayCreateRequest       $request
     * @param EasyPayWalletCreateCommand $command
     *
     * @return EasyPayWallet
     */
    public function store(EasyPayCreateRequest $request, EasyPayWalletCreateCommand $command): EasyPayWallet
    {
        /* @var $user User */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($request->getDto(), $user->seller);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return EasyPayWallet|Builder|Collection|Model|null
     */
    public function show($id)
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $easyPay = EasyPayWallet::whereSellerId($user->seller_id)
            ->with(
                [
                    'proxy',
                    'transactions.order',
                ]
            )
            ->whereNumber($id)
            ->firstOrFail();

        $easyPay->append(['balance', 'is_limit']);
        $easyPay->setHidden(['transactions']);

        return $easyPay;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EasyPayEditRequest       $request
     * @param EasyPayWalletEditCommand $command
     * @param string                   $id
     *
     * @return EasyPayWallet|Builder|Model|void
     */
    public function update(EasyPayEditRequest $request, EasyPayWalletEditCommand $command, string $id)
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
     * @param EasyPayWalletDeleteCommand $command
     * @param string                     $id
     *
     * @return void
     */
    public function destroy(EasyPayWalletDeleteCommand $command, string $id): void
    {
        /* @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($id, $user->seller);
    }

    /**
     * @return EasyPayWallet[]|Builder[]|Collection
     */
    public function getSelect()
    {
        /* @var User $user */
        $user = auth()->user();

        return EasyPayWallet::whereSellerId($user->seller_id)
            ->get(['name as text', 'number as value']);
    }

    /**
     * @param CheckAccountLoginCommand $command
     * @param string                   $id
     *
     * @return EasyPayWallet
     */
    public function check(CheckAccountLoginCommand $command, string $id): EasyPayWallet
    {
        /* @var User $user */
        $user = auth()->user();

        return $command->execute((int) $id, $user->seller);
    }

    /**
     * @param RestoreBalanceCommand $command
     * @param string                $number
     */
    public function restoreBalance(RestoreBalanceCommand $command, string $number): void
    {
        /* @var User $user */
        $user = auth()->user();

        $command->execute((int) $number, $user->seller);
    }
}

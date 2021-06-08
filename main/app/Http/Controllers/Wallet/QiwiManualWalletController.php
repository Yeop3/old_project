<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\Wallet\QiwiManual\CreateQiwiManualWalletRequest;
use App\Http\Requests\Wallet\QiwiManual\UpdateQiwiManualWalletRequest;
use App\Models\User;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Wallet\QiwiManual\ClearSoftDeletedQiwiManualWalletsCommand;
use App\Services\Wallet\QiwiManual\Create\CreateQiwiManualWalletCommand;
use App\Services\Wallet\QiwiManual\DeleteQiwiManualWalletCommand;
use App\Services\Wallet\QiwiManual\RestoreQiwiManualWalletCommand;
use App\Services\Wallet\QiwiManual\Update\UpdateQiwiManualWalletCommand;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;

/**
 * Class QiwiManualWalletController.
 */
class QiwiManualWalletController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return QiwiManualWallet::whereSellerId($user->seller_id)->with([
            'payments',
            'orders',
            'ordersPartiallyPaid',
            'ordersAwaitingPayment',
        ])
            ->paginate(20);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function indexDeleted(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return QiwiManualWallet::whereSellerId($user->seller_id)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->with(['orders', 'ordersAwaitingPayment', 'ordersPartiallyPaid'])
            ->paginate(20);
    }

    /**
     * @param $number
     *
     * @return QiwiManualWallet|Builder|Model
     */
    public function show($number)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return QiwiManualWallet::whereSellerId($user->seller_id)
            ->whereNumber($number)
            ->withCount('orders', 'ordersAwaitingPayment', 'ordersPartiallyPaid', 'payments')
            ->firstOrFail();
    }

    /**
     * @param $number
     *
     * @return QiwiManualWallet|Builder|Model
     */
    public function showDeleted($number)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return QiwiManualWallet::whereSellerId($user->seller_id)
            ->withTrashed()
            ->whereNumber($number)
            ->withCount('orders', 'ordersAwaitingPayment', 'ordersPartiallyPaid', 'payments')
            ->firstOrFail();
    }

    /**
     * @param CreateQiwiManualWalletRequest $request
     * @param CreateQiwiManualWalletCommand $command
     *
     * @return QiwiManualWallet
     */
    public function store(CreateQiwiManualWalletRequest $request, CreateQiwiManualWalletCommand $command): QiwiManualWallet
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateQiwiManualWalletRequest $request
     * @param UpdateQiwiManualWalletCommand $command
     * @param $id
     *
     * @return QiwiManualWallet
     */
    public function update(UpdateQiwiManualWalletRequest $request, UpdateQiwiManualWalletCommand $command, $id): QiwiManualWallet
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteQiwiManualWalletCommand $command
     * @param $number
     *
     * @throws Exception
     */
    public function destroy(DeleteQiwiManualWalletCommand $command, $number): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($number, $user->seller);
    }

    /**
     * @param DeleteQiwiManualWalletCommand $command
     * @param $number
     *
     * @throws Exception
     */
    public function destroyForever(DeleteQiwiManualWalletCommand $command, $number): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($number, $user->seller, true);
    }

    /**
     * @param ClearSoftDeletedQiwiManualWalletsCommand $command
     */
    public function clearDeleted(ClearSoftDeletedQiwiManualWalletsCommand $command): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($user->seller);
    }

    /**
     * @param RestoreQiwiManualWalletCommand $command
     * @param $number
     */
    public function restore(RestoreQiwiManualWalletCommand $command, $number): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        $command->execute($number, $user->seller);
    }
}

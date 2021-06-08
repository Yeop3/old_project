<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\Wallet\QiwiManualPayment\CreateQiwiManualPaymentRequest;
use App\Http\Requests\Wallet\QiwiManualPayment\UpdateQiwiManualPaymentRequest;
use App\Models\User;
use App\Models\Wallet\QiwiManualPayment;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Wallet\QiwiManualPayment\Create\CreateQiwiManualPaymentCommand;
use App\Services\Wallet\QiwiManualPayment\Update\UpdateQiwiManualPaymentCommand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;

/**
 * Class QiwiManualPaymentController.
 */
class QiwiManualPaymentController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();
        $qiwi = QiwiManualPayment::whereSellerId($user->seller_id);
        if ($number = request('number')) {
            $qiwi->where('number', $number);
        }

        if ($phone = request('phone')) {
            $qiwiWallet = QiwiManualWallet::whereSellerId($user->seller_id)->where('number', $phone)->first();
            if ($qiwiWallet) {
                $qiwi->where('qiwi_manual_wallet_id', $qiwiWallet->id);
            }
        }

        return $qiwi->with([
            'wallet',
            'order',
            'order.client',
            'order.shift',
            'order.shift.operator',
        ])
            ->paginate(20);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return QiwiManualPayment::with(['order'])->whereSellerId($user->seller_id)->whereNumber($id)->firstOrFail();
    }

    /**
     * @param CreateQiwiManualPaymentRequest $request
     * @param CreateQiwiManualPaymentCommand $command
     *
     * @return QiwiManualPayment
     */
    public function store(CreateQiwiManualPaymentRequest $request, CreateQiwiManualPaymentCommand $command): QiwiManualPayment
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateQiwiManualPaymentRequest $request
     * @param UpdateQiwiManualPaymentCommand $command
     * @param $id
     *
     * @return QiwiManualPayment
     */
    public function update(UpdateQiwiManualPaymentRequest $request, UpdateQiwiManualPaymentCommand $command, $id): QiwiManualPayment
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403);
        }

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @return QiwiManualWallet[]|Builder[]|Collection
     */
    public function phonesSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return QiwiManualWallet::whereSellerId($user->seller_id)->where('active', true)->get(['phone as text', 'number as value']);
    }
}

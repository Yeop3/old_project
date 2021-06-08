<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual;

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\Exceptions\QiwiWalletOrderException;
use Exception;

/**
 * Class DeleteQiwiManualWalletCommand.
 */
final class DeleteQiwiManualWalletCommand
{
    /**
     * @param $walletNumber
     * @param Seller $seller
     * @param bool   $forever
     *
     * @throws Exception
     */
    public function execute($walletNumber, Seller $seller, bool $forever = false): void
    {
        $wallet = QiwiManualWallet::whereSellerId($seller->id)
            ->whereNumber($walletNumber)
            ->withTrashed()
            ->firstOrFail();

        $order = $wallet->orders()
            ->whereIn('status', [OrderStatus::STATUS_PARTIALLY_PAID, OrderStatus::STATUS_AWAITING_PAYMENT])
            ->exists();

        if ($order) {
            throw new QiwiWalletOrderException('У данного кошелька есть ожидающие заказы');
        }

        if ($forever) {
            $wallet->forceDelete();

            return;
        }

        $wallet->delete();
    }
}

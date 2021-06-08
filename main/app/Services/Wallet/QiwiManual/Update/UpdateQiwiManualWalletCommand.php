<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual\Update;

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;

/**
 * Class UpdateQiwiManualWalletCommand.
 */
final class UpdateQiwiManualWalletCommand
{
    public function execute(int $walletNumber, Seller $seller, UpdateQiwiManualWalletDto $dto): QiwiManualWallet
    {
        $qiwiManualWallet = QiwiManualWallet::whereSellerId($seller->id)->whereNumber($walletNumber)->firstOrFail();

        $qiwiManualWallet->active = $dto->isActive();
        $qiwiManualWallet->note = $dto->getNote();
        $qiwiManualWallet->min_paid_orders_count = $dto->getMinPaidOrdersCountForShowing();

        $qiwiManualWallet->save();

        return $qiwiManualWallet;
    }
}

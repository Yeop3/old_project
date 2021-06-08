<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual;

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;

/**
 * Class ClearSoftDeletedQiwiManualWalletsCommand.
 */
final class ClearSoftDeletedQiwiManualWalletsCommand
{
    public function execute(Seller $seller): void
    {
        $wallets = QiwiManualWallet::whereSellerId($seller->id)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->get();

        foreach ($wallets as $wallet) {
            $wallet->forceDelete();
        }
    }
}

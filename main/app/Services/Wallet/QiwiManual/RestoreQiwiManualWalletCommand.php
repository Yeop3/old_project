<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual;

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;

/**
 * Class RestoreQiwiManualWalletCommand.
 */
final class RestoreQiwiManualWalletCommand
{
    /**
     * @param $walletNumber
     * @param Seller $seller
     */
    public function execute($walletNumber, Seller $seller): void
    {
        $wallet = QiwiManualWallet::whereSellerId($seller->id)
            ->whereNumber($walletNumber)
            ->withTrashed()
            ->whereNotNull('deleted_at')
            ->firstOrFail();

        $wallet->restore();
    }
}

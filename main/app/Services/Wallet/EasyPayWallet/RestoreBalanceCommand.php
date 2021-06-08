<?php

namespace App\Services\Wallet\EasyPayWallet;

use App\Models\Seller;
use App\Models\Wallet\EasyPayWallet;

/**
 * Class RestoreBalanceCommand.
 */
class RestoreBalanceCommand
{
    /**
     * @param int    $number
     * @param Seller $seller
     */
    public function execute(int $number, Seller $seller): void
    {
        $easyPayWallet = EasyPayWallet::whereSellerId($seller->id)
            ->whereNumber($number)
            ->firstOrFail();

        $easyPayWallet->restore_date = now();

        $easyPayWallet->save();
    }
}

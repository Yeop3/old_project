<?php

namespace App\Services\Wallet\EasyPayWallet\Delete;

use App\Models\Seller;
use App\Models\Wallet\EasyPayWallet;

/**
 * Class EasyPayWalletDeleteCommand.
 */
class EasyPayWalletDeleteCommand
{
    public function execute(string $id, Seller $seller): void
    {
        EasyPayWallet::whereSellerId($seller->id)->where('number', $id)->firstOrFail()->delete();
    }
}

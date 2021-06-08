<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\Delete;

use App\Models\Seller;
use App\Models\Wallet\GlobalMoneyWallet;

/**
 * Class GlobalMoneyWalletDeleteCommand.
 */
final class GlobalMoneyWalletDeleteCommand
{
    public function execute(string $id, Seller $seller): void
    {
        GlobalMoneyWallet::whereSellerId($seller->id)->where('number', $id)->firstOrFail()->delete();
    }
}

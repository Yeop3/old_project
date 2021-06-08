<?php

namespace App\Services\Wallet\Crypto\Delete;

use App\Models\Seller;
use App\Models\Wallet\CryptoWallet;
use Exception;

/**
 * Class CryptoWalletDeleteCommand.
 */
class CryptoWalletDeleteCommand
{
    /**
     * @param int    $id
     * @param Seller $seller
     *
     * @throws Exception
     */
    public function execute(int $id, Seller $seller): void
    {
        CryptoWallet::whereSellerId($seller->id)->where('number', $id)->firstOrFail()->delete();
    }
}

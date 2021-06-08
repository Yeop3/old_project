<?php

declare(strict_types=1);

namespace App\Services\Wallet\Kuna\Delete;

use App\Models\Seller;
use App\Models\Wallet\KunaAccount;
use Exception;

/**
 * Class KunaWalletDeleteCommand.
 */
final class KunaWalletDeleteCommand
{
    /**
     * @param int    $id
     * @param Seller $seller
     *
     * @throws Exception
     */
    public function execute(int $id, Seller $seller): void
    {
        KunaAccount::whereSellerId($seller->id)->where('number', $id)->firstOrFail()->delete();
    }
}

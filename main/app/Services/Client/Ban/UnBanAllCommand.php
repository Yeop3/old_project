<?php

declare(strict_types=1);

namespace App\Services\Client\Ban;

use App\Models\Client;
use App\Models\Seller;

/**
 * Class UnBanAllCommand.
 */
final class UnBanAllCommand
{
    public function execute(Seller $seller): void
    {
        Client::whereSellerId($seller->id)->whereNotNull('ban_expires_at')
            ->update(['ban_expires_at' => null]);
    }
}

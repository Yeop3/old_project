<?php

declare(strict_types=1);

namespace App\Services\Client\MultiBan;

use App\Models\Client;
use App\Models\Seller;

/**
 * Class MultiBanClientCommand.
 */
final class MultiBanClientCommand
{
    public function execute(MultiBanClientDto $dto, Seller $seller): void
    {
        Client::whereSellerId($seller->id)
            ->whereIn('number', $dto->getNumbers())
            ->update(['ban_expires_at' => now()->addDays($dto->getPeriod())]);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Client\MultiBlackList;

use App\Models\Client;
use App\Models\Seller;

/**
 * Class MultiBlackListClientCommand.
 */
final class MultiBlackListClientCommand
{
    public function execute(MultiBlackListClientDto $dto, Seller $seller): void
    {
        Client::whereSellerId($seller->id)
            ->whereIn('number', $dto->getNumbers())
            ->update(['in_black_list' => true]);
    }
}

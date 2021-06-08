<?php

declare(strict_types=1);

namespace App\Services\Client\Update;

use App\Models\Client;
use App\Models\Seller;

/**
 * Class UpdateClientCommand.
 */
final class UpdateClientCommand
{
    public function execute(int $clientNumber, Seller $seller, UpdateClientDto $dto): Client
    {
        $client = Client::whereNumber($clientNumber)->whereSellerId($seller->id)->firstOrFail();

        $client->note = $dto->getNote();
        $client->discount_value = $dto->getDiscountValue();
        $client->discount_priority = $dto->getDiscountPriority();

        $client->save();

        return $client;
    }
}

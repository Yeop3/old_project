<?php

namespace App\Services\Stoker\Update;

use App\Models\Bot;
use App\Models\Client;
use App\Models\Location;
use App\Models\ProductType;
use App\Models\Stoker;
use App\Services\Stoker\StokerDto;

/**
 * Class UpdateStokerCommand.
 */
class UpdateStokerCommand
{
    public function execute(int $sellerId, Stoker $stoker, StokerDto $dto): Stoker
    {
        $location = Location::whereSellerId($sellerId)->whereNumber($dto->getLocationNumber())->firstOrFail();
        $bot = Bot::whereSellerId($sellerId)->whereNumber($dto->getSourceNumber())->firstOrFail();
        $productType = ProductType::whereSellerId($sellerId)->whereNumber($dto->getProductTypeNumber())->firstOrFail();
        $client = Client::whereSellerId($sellerId)->whereNumber($dto->getClientNumber())->firstOrFail();

        $stoker->source_id = $bot->id;
        $stoker->source_type = 'bot';
        $stoker->product_type_id = $productType->id;
        $stoker->location_id = $location->id;
        $stoker->client_id = $client->id;

        $stoker->save();

        return $stoker;
    }
}

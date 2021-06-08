<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Bot;
use App\Models\Client;
use App\Models\Seller;
use App\Services\Client\Create\ClientCreateByBotDto;
use App\Services\Client\Create\HandleClientByBotCommand;

final class ClientHelper
{
    public static function createBotClient(Seller $seller): Client
    {
        $command = app()->make(HandleClientByBotCommand::class);

        $dto = new ClientCreateByBotDto(123123123, [], 'some name', 'some username');

        $bot = Bot::whereSellerId($seller->id)->first();

        return $command->execute($bot, $dto);
    }
}

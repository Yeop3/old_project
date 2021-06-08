<?php

namespace App\Services\Client\Create;

use App\Models\Bot;
use App\Models\Client;
use App\Models\ClientHistory;
use App\Services\Discount\VO\DiscountValue;
use Illuminate\Support\Facades\DB;

/**
 * Class HandleClientByBotCommand.
 */
class HandleClientByBotCommand
{
    public function execute(Bot $bot, ClientCreateByBotDto $dto): Client
    {
        $client = Client::withTrashed()
            ->whereSellerId($bot->seller_id)
            ->whereTelegramId($dto->getTelegramId())
            ->first();

        DB::beginTransaction();

        if ($client) {
            $this->handleClientInfoChanged($client, $dto);
        } else {
            $client = $this->createClientInstance($bot, $dto);
        }

        $client->source_id = $bot->id;
        $client->source_type = 'bot';

        if ($client->trashed()) {
            $client->deleted_at = null;
        }

        $client->visited_at = now();

        $client->save();

        $client->bots()->sync(([...$client->bots->pluck('id'), $bot->id]));

        DB::commit();

        return $client;
    }

    private function createClientInstance(Bot $bot, ClientCreateByBotDto $dto): Client
    {
        $client = new Client();

        $client->name = $dto->getName();
        $client->username = $dto->getUsername();
        $client->info = $dto->getInfo();
        $client->telegram_id = $dto->getTelegramId();

        $client->discount_value = new DiscountValue(0.0);
        $client->seller_id = $bot->seller_id;
        $client->discount_priority = 100;

        return $client;
    }

    private function handleClientInfoChanged(Client $client, ClientCreateByBotDto $dto): void
    {
        $lastItem = $client->history()->orderByDesc('created_at')->first();

        if (!$lastItem) {
            $lastItem = $client;
        }

        if (
            $lastItem->username !== $dto->getUsername()
            || $lastItem->name !== $dto->getName()
        ) {
            $clientHistoryItem = new ClientHistory();

            $clientHistoryItem->client_id = $client->id;
            $clientHistoryItem->name = $dto->getName();
            $clientHistoryItem->username = $dto->getUsername();
            $clientHistoryItem->info = $dto->getInfo();

            $clientHistoryItem->save();
        }
    }
}

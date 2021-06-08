<?php

namespace App\Services\Dispatch\Create;

use App\Jobs\DispatchJob;
use App\Models\Bot;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\Seller;
use Illuminate\Support\Collection;

/**
 * Class DispatchCreateCommand.
 */
class DispatchCreateCommand
{
    public function execute(DispatchCreateDto $dto, Seller $seller): void
    {
        foreach ($dto->getBotsNumbers() as $botNumber) {
            $bot = Bot::whereSellerId($seller->id)->where('number', $botNumber)->first();

            if (!$bot) {
                continue;
            }

            $dispatch = new Dispatch();
            $dispatch->messages = $dto->getMessages();
            $dispatch->bot_id = $bot->id;
            $dispatch->seller_id = $seller->id;
            $dispatch->save();
            Client::whereSellerId($seller->id)
                ->whereNull('ban_expires_at')
                ->where('in_black_list', '=', false)
                ->where('source_id', $dispatch->bot_id)
                ->chunk(
                    100,
                    static function (Collection $clients) use ($dispatch, $seller) {
                        $clients->each(static function (Client $client) use ($dispatch, $seller) {
                            DispatchJob::dispatch($dispatch, $client, $seller);
                        });
                    }
                );
        }
    }
}

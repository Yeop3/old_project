<?php

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\Seller;
use App\VO\SourceType;
use Exception;

/**
 * Class NotifyDispatch.
 */
final class NotifyDispatch
{
    /**
     * @param Dispatch $dispatch
     * @param Client   $client
     * @param Seller   $seller
     *
     * @throws Exception
     */
    public function execute(Dispatch $dispatch, Client $client, Seller $seller): void
    {
        if ($client->source_type === SourceType::TYPE_SITE) {
            return;
        }
        \App\Models\Bot::whereSellerId($seller->id)
            ->where('id', $dispatch->bot_id)
            ->get()
            ->each(function (\App\Models\Bot $botModel) use ($dispatch, $client) {
                $bot = new Bot($botModel);
                $bot->say(
                    $dispatch->messages,
                    [$client->telegram_id],
                    $botModel->getBotDriver()
                );
            });
    }
}

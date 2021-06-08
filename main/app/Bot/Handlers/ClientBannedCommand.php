<?php

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Client;
use App\VO\SourceType;
use Carbon\Carbon;
use Exception;
use RuntimeException;

/**
 * Class ClientBannedCommand.
 */
class ClientBannedCommand
{
    /**
     * @param Client $client
     *
     * @throws Exception
     */
    public function execute(Client $client): void
    {
        if ($client->source_type !== SourceType::TYPE_BOT) {
            throw new RuntimeException('implemented only bot client source');
        }

        /** @var Bot $botModel */
        $botModel = Bot::where('id', $client->source_id)->first();

        $bot = new \App\Bot\Bot($botModel);

        $diff = Carbon::make(now());

        if (!$diff && !$client->ban_expires_at) {
            return;
        }

        $diff = $diff->diffForHumans($client->ban_expires_at);

        $bot->say(
            str_replace([
                '{ban-duration}',
            ], [
                $diff,
            ], '❗️ <b>Вы забанены оператором!</b>
{ban-duration} конца блокировки'),
            [$client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

<?php

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Client;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class ClientUnbannedCommand.
 */
class ClientUnbannedCommand
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

        if ($client->ban_expires_at) {
            return;
        }

        $bot->say(
            '❗️ <b>Вы были разбаненны оператором!</b>',
            [$client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

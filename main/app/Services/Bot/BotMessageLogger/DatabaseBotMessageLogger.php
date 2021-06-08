<?php

declare(strict_types=1);

namespace App\Services\Bot\BotMessageLogger;

use App\Models\BotMessage;
use App\Models\Client;

/**
 * Class DatabaseBotMessageLogger.
 */
final class DatabaseBotMessageLogger implements BotMessageLogger
{
    public function log(Client $client, string $text, bool $fromBot): void
    {
        $botMessage = new BotMessage();

        $botMessage->client_id = $client->id;
        $botMessage->bot_id = $client->source_id;
        $botMessage->from_bot = $fromBot;
        $botMessage->text = $text;

        $botMessage->save();
    }
}

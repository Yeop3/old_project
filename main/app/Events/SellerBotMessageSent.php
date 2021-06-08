<?php

namespace App\Events;

use App\Models\Client;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SellerBotMessageSent.
 */
class SellerBotMessageSent
{
    use Dispatchable;
    use SerializesModels;

    private Client $client;
    private MessageFromBotDto $messageFromBotDto;

    public function __construct(Client $client, MessageFromBotDto $messageFromBotDto)
    {
        $this->client = $client;
        $this->messageFromBotDto = $messageFromBotDto;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getMessageFromBotDto(): MessageFromBotDto
    {
        return $this->messageFromBotDto;
    }
}

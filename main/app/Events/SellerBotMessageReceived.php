<?php

namespace App\Events;

use App\Models\Client;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SellerBotMessageReceived.
 */
class SellerBotMessageReceived
{
    use Dispatchable;
    use SerializesModels;

    private Client $client;
    private IncomingMessage $incomingMessage;

    public function __construct(Client $client, IncomingMessage $incomingMessage)
    {
        $this->client = $client;
        $this->incomingMessage = $incomingMessage;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIncomingMessage(): IncomingMessage
    {
        return $this->incomingMessage;
    }
}

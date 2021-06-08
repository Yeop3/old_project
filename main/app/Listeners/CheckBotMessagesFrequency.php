<?php

namespace App\Listeners;

use App\Bot\Commands\AntiSpamChecker;
use App\Events\SellerBotMessageReceived;

/**
 * Class CheckBotMessagesFrequency.
 */
class CheckBotMessagesFrequency
{
    private AntiSpamChecker $antiSpamHandler;

    public function __construct(AntiSpamChecker $antiSpamHandler)
    {
        $this->antiSpamHandler = $antiSpamHandler;
    }

    public function handle(SellerBotMessageReceived $event): void
    {
        $client = $event->getClient();

        if ($client->driver()->exists()) {
            return;
        }

        $this->antiSpamHandler->handleFrequentMessages($client);
    }
}

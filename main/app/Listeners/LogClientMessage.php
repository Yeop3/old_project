<?php

namespace App\Listeners;

use App\Events\SellerBotMessageReceived;
use App\Services\Bot\BotMessageLogger\LogMessageFromClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class LogClientMessage.
 */
class LogClientMessage implements ShouldQueue
{
    use InteractsWithQueue;

    private LogMessageFromClient $logMessageFromClient;

    public function __construct(LogMessageFromClient $logMessageFromClient)
    {
        $this->logMessageFromClient = $logMessageFromClient;
    }

    /**
     * @param SellerBotMessageReceived $event
     */
    public function handle(SellerBotMessageReceived $event): void
    {
        ($this->logMessageFromClient)($event->getClient(), $event->getIncomingMessage());
    }
}

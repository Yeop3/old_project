<?php

namespace App\Listeners;

use App\Events\SellerBotMessageSent;
use App\Services\Bot\BotMessageLogger\LogMessageFromBot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class LogBotMessage.
 */
class LogBotMessage implements ShouldQueue
{
    use InteractsWithQueue;

    private LogMessageFromBot $logMessageFromBot;

    public function __construct(LogMessageFromBot $logMessageFromBot)
    {
        $this->logMessageFromBot = $logMessageFromBot;
    }

    /**
     * @param SellerBotMessageSent $event
     */
    public function handle(SellerBotMessageSent $event): void
    {
        ($this->logMessageFromBot)($event->getClient(), $event->getMessageFromBotDto());
    }
}

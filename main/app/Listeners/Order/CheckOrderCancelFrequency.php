<?php

namespace App\Listeners\Order;

use App\Bot\Commands\AntiSpamChecker;
use App\Events\Order\OrderCanceledEventForFrequencyChecking;
use Exception;

/**
 * Class CheckOrderCancelFrequency.
 */
class CheckOrderCancelFrequency
{
    private AntiSpamChecker $antiSpamHandler;

    public function __construct(AntiSpamChecker $antiSpamHandler)
    {
        $this->antiSpamHandler = $antiSpamHandler;
    }

    /**
     * @param OrderCanceledEventForFrequencyChecking $event
     *
     * @throws Exception
     */
    public function handle(OrderCanceledEventForFrequencyChecking $event): void
    {
        $this->antiSpamHandler->handelFrequentCanceledOrders($event->getOrder()->client);
    }
}

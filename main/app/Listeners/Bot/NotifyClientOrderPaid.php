<?php

namespace App\Listeners\Bot;

use App\Bot\Notifications\NotifyOrderPaid;
use App\Events\Order\OrderPaid;

/**
 * Class NotifyClientOrderPaid.
 */
class NotifyClientOrderPaid
{
    private NotifyOrderPaid $notifyOrderPaid;

    public function __construct(NotifyOrderPaid $notifyOrderPaid)
    {
        $this->notifyOrderPaid = $notifyOrderPaid;
    }

    public function handle(OrderPaid $event): void
    {
        $this->notifyOrderPaid->execute($event->getOrder());
    }
}

<?php

namespace App\Listeners\Bot;

use App\Bot\Notifications\NotifyOrderPartiallyPaid;
use App\Events\Order\OrderPartiallyPaid;

/**
 * Class NotifyClientOrderPartiallyPaid.
 */
class NotifyClientOrderPartiallyPaid
{
    private NotifyOrderPartiallyPaid $notifyOrderPartiallyPaid;

    public function __construct(NotifyOrderPartiallyPaid $notifyOrderPartiallyPaid)
    {
        $this->notifyOrderPartiallyPaid = $notifyOrderPartiallyPaid;
    }

    public function handle(OrderPartiallyPaid $event): void
    {
        $this->notifyOrderPartiallyPaid->execute($event->getOrder());
    }
}

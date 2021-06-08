<?php

namespace App\Listeners\Bot;

use App\Bot\Notifications\NotifyOrderReminder;
use App\Events\Order\ReminderOrder;

/**
 * Class NotifyReminderOrder.
 */
class NotifyReminderOrder
{
    private NotifyOrderReminder $notifyOrderReminder;

    public function __construct(NotifyOrderReminder $notifyOrderReminder)
    {
        $this->notifyOrderReminder = $notifyOrderReminder;
    }

    public function handle(ReminderOrder $event): void
    {
        $this->notifyOrderReminder->execute($event->getOrder());
    }
}

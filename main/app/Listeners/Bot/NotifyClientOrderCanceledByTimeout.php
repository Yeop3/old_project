<?php

namespace App\Listeners\Bot;

use App\Bot\Notifications\NotifyOrderCanceledByTimeout;
use App\Events\Order\OrderCanceledByTimeout;

/**
 * Class NotifyClientOrderCanceledByTimeout.
 */
class NotifyClientOrderCanceledByTimeout
{
    private NotifyOrderCanceledByTimeout $notifyOrderCanceledByTimeout;

    public function __construct(NotifyOrderCanceledByTimeout $notifyOrderCanceledByTimeout)
    {
        $this->notifyOrderCanceledByTimeout = $notifyOrderCanceledByTimeout;
    }

    public function handle(OrderCanceledByTimeout $event): void
    {
        $this->notifyOrderCanceledByTimeout->execute($event->getOrder());
    }
}

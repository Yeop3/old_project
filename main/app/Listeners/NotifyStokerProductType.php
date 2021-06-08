<?php

namespace App\Listeners;

use App\Bot\Notifications\NotifyStokerCount;
use App\Events\StokerProductTypeNotifyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class NotifyStokerProductType.
 */
class NotifyStokerProductType implements ShouldQueue
{
    private NotifyStokerCount $notifyStokerCount;

    public function __construct(NotifyStokerCount $notifyStokerCount)
    {
        $this->notifyStokerCount = $notifyStokerCount;
    }

    public function handle(StokerProductTypeNotifyEvent $event): void
    {
        $this->notifyStokerCount->execute($event->getProduct());
    }
}

<?php

namespace App\Listeners\Bot;

use App\Bot\Notifications\NotifyOrderCanceledByOperator;
use App\Events\Order\OrderCanceledByOperator;
use Exception;

/**
 * Class NotifyOperatorOrderCancel.
 */
class NotifyOperatorOrderCancel
{
    private NotifyOrderCanceledByOperator $notifyOrderCanceledByOperator;

    public function __construct(NotifyOrderCanceledByOperator $notifyOrderCanceledByOperator)
    {
        $this->notifyOrderCanceledByOperator = $notifyOrderCanceledByOperator;
    }

    /**
     * @param OrderCanceledByOperator $event
     *
     * @throws Exception
     */
    public function handle(OrderCanceledByOperator $event): void
    {
        $this->notifyOrderCanceledByOperator->execute($event->getOrder());
    }
}

<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderSavingEvent;
use App\Services\Order\OrderSavingCommand;

/**
 * Class OrderSavingListener.
 */
class OrderSavingListener
{
    private OrderSavingCommand $command;

    /**
     * Create the event listener.
     *
     * @param OrderSavingCommand $command
     */
    public function __construct(OrderSavingCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Handle the event.
     *
     * @param OrderSavingEvent $event
     *
     * @return void
     */
    public function handle(OrderSavingEvent $event): void
    {
        $this->command->execute($event->getOrder());
    }
}

<?php

namespace App\Services\Order;

use App\Events\Order\ReminderOrder;
use App\Models\Order;
use Illuminate\Events\Dispatcher;

/**
 * Class ReminderOrderHandler.
 */
final class ReminderOrderHandler
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(Order $order): void
    {
        $this->dispatcher->dispatch(new ReminderOrder($order));
    }
}

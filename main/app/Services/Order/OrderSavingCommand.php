<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;

/**
 * Class OrderSavingCommand.
 */
final class OrderSavingCommand
{
    public function execute(Order $order): void
    {
        $status = $order->status;
        if ($status->isCancelOrderStatus()) {
            $order->canceled_at = now();
        } else {
            $order->canceled_at = null;
        }
    }
}

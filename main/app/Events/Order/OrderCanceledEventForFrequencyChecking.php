<?php

declare(strict_types=1);

namespace App\Events\Order;

use App\Models\Order;

/**
 * Interface OrderCanceledEventForFrequencyChecking.
 */
interface OrderCanceledEventForFrequencyChecking
{
    public function getOrder(): Order;
}

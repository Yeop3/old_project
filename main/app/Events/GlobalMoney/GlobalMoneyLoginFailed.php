<?php

namespace App\Events\GlobalMoney;

use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class GlobalMoneyLoginFailed.
 */
class GlobalMoneyLoginFailed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private GlobalMoneyLoginData $globalMoneyLoginData;

    /**
     * Create a new event instance.
     *
     * @param GlobalMoneyLoginData $globalMoneyLoginData
     */
    public function __construct(GlobalMoneyLoginData $globalMoneyLoginData)
    {
        $this->globalMoneyLoginData = $globalMoneyLoginData;
    }

    public function getLoginData(): GlobalMoneyLoginData
    {
        return $this->globalMoneyLoginData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

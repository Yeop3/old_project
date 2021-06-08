<?php

namespace App\Events\Wallet;

use App\Services\Wallet\EasyPayWallet\VO\EasyPayLoginData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class EasyPayLoginFailEvent.
 */
class EasyPayLoginFailEvent
{
    use Dispatchable;
    use SerializesModels;

    private EasyPayLoginData $loginData;

    /**
     * Create a new event instance.
     *
     * @param EasyPayLoginData $loginData
     */
    public function __construct(EasyPayLoginData $loginData)
    {
        $this->loginData = $loginData;
    }

    /**
     * @return EasyPayLoginData
     */
    public function getLoginData(): EasyPayLoginData
    {
        return $this->loginData;
    }
}

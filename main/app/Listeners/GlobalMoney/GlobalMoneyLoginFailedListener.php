<?php

namespace App\Listeners\GlobalMoney;

use App\Events\GlobalMoney\GlobalMoneyLoginFailed;
use App\Models\Wallet\GlobalMoneyWallet;

/**
 * Class GlobalMoneyLoginFailedListener.
 */
class GlobalMoneyLoginFailedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param GlobalMoneyLoginFailed $event
     *
     * @return void
     */
    public function handle(GlobalMoneyLoginFailed $event): void
    {
        $wallets = GlobalMoneyWallet::whereLogin($event->getLoginData()->getLogin())->where('password', $event->getLoginData()->getPassword())->get();

        foreach ($wallets as $wallet) {
            $wallet->wrong_credentials = 1;
            $wallet->save();
        }
    }
}

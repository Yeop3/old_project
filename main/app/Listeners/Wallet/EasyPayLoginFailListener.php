<?php

namespace App\Listeners\Wallet;

use App\Events\Wallet\EasyPayLoginFailEvent;
use App\Services\Wallet\EasyPayWallet\EasyPayLoginFailCommand;

/**
 * Class EasyPayLoginFailListener.
 */
class EasyPayLoginFailListener
{
    private EasyPayLoginFailCommand $command;

    /**
     * EasyPayLoginFailListener constructor.
     *
     * @param EasyPayLoginFailCommand $command
     */
    public function __construct(EasyPayLoginFailCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Handle the event.
     *
     * @param EasyPayLoginFailEvent $event
     *
     * @return void
     */
    public function handle(EasyPayLoginFailEvent $event): void
    {
        $this->command->execute($event->getLoginData());
    }
}

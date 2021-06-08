<?php

namespace App\Listeners;

use App\Bot\Handlers\ClientBannedCommand;
use App\Events\ClientBannedEvent;
use Exception;

/**
 * Class ClientBannedListener.
 */
class ClientBannedListener
{
    private ClientBannedCommand $clientBannedCommand;

    /**
     * Create the event listener.
     *
     * @param ClientBannedCommand $clientBannedCommand
     */
    public function __construct(ClientBannedCommand $clientBannedCommand)
    {
        $this->clientBannedCommand = $clientBannedCommand;
    }

    /**
     * Handle the event.
     *
     * @param ClientBannedEvent $event
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle(ClientBannedEvent $event): void
    {
        $this->clientBannedCommand->execute($event->getClient());
    }
}

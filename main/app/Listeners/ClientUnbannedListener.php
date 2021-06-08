<?php

namespace App\Listeners;

use App\Bot\Handlers\ClientUnbannedCommand;
use App\Events\ClientUnbannedEvent;
use Exception;

/**
 * Class ClientUnbannedListener.
 */
class ClientUnbannedListener
{
    private ClientUnbannedCommand $clientUnbannedCommand;

    /**
     * Create the event listener.
     *
     * @param ClientUnbannedCommand $clientUnbannedCommand
     */
    public function __construct(ClientUnbannedCommand $clientUnbannedCommand)
    {
        $this->clientUnbannedCommand = $clientUnbannedCommand;
    }

    /**
     * Handle the event.
     *
     * @param ClientUnbannedEvent $event
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle(ClientUnbannedEvent $event): void
    {
        $this->clientUnbannedCommand->execute($event->getClient());
    }
}

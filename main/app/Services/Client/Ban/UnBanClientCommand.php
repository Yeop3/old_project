<?php

namespace App\Services\Client\Ban;

use App\Events\ClientUnbannedEvent;
use App\Models\Client;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class UnBanClientCommand.
 */
final class UnBanClientCommand
{
    private Dispatcher $dispatcher;

    /**
     * BanClientCommand constructor.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Client $client
     */
    public function execute(Client $client): void
    {
        $client->ban_expires_at = null;
        $client->save();

        $this->dispatcher->dispatch(new ClientUnbannedEvent($client));
    }
}

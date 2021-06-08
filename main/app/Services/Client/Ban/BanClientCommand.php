<?php

namespace App\Services\Client\Ban;

use App\Events\ClientBannedEvent;
use App\Models\Client;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class BanClientCommand.
 */
final class BanClientCommand
{
    /**
     * @var Dispatcher
     */
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
     * @param BanClientDto $dto
     * @param Client       $client
     */
    public function execute(BanClientDto $dto, Client $client): void
    {
        $client->ban_expires_at = now()->addDays($dto->getDays());
        $client->save();

        $this->dispatcher->dispatch(new ClientBannedEvent($client));
    }
}

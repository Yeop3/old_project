<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientUnbannedEvent.
 */
class ClientUnbannedEvent
{
    use Dispatchable;
    use SerializesModels;

    private Client $client;

    /**
     * ClientUnbannedEvent constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}

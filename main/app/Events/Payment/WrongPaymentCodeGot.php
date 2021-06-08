<?php

namespace App\Events\Payment;

use App\Models\Client;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class WrongPaymentCodeGot.
 */
class WrongPaymentCodeGot
{
    use Dispatchable;
    use SerializesModels;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}

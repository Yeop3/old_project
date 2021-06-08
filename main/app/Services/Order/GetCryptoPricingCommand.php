<?php

declare(strict_types=1);

namespace App\Services\Order;

use GuzzleHttp\Client;

/**
 * Class GetCryptoPricingCommand.
 */
class GetCryptoPricingCommand
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $url = 'https://api.exmo.com/v1.1/ticker';

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}

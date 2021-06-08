<?php

declare(strict_types=1);

namespace App\Services\Wallet\Ethereum;

use GuzzleHttp\Client;

/**
 * Class GetEthereumTransactionsFromBlockchainApi.
 */
final class GetEthereumTransactionsFromBlockchainApi
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    public function execute(string $address, int $offset = 0, int $limit = 50): array
    {
        $response = $this->client->get("https://blockchain.info/address/$address?format=json&offset=$offset&limit=$limit");

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
}

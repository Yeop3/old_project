<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitcoin;

use App\Models\Proxy;
use GuzzleHttp\Client;

/**
 * Class GetBitcoinWalletInfoFromBlockCypher.
 */
final class GetBitcoinWalletInfoFromBlockCypher
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            //            'http_errors' => false,
        ]);
    }

    public function execute(string $address, int $confirmations = 3, ?Proxy $proxy = null): array
    {
        $response = $this->client->get(
            "https://api.blockcypher.com/v1/btc/main/addrs/$address?confirmations=$confirmations",
            [
                'proxy'   => $proxy ? $proxy->toString() : null,
                'timeout' => 10,
            ]
        );

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitaps;

use App\Models\Proxy;
use App\Services\Wallet\Bitaps\Exceptions\BitapsApiNotAvailableException;
use App\VO\CryptoCurrency;
use GuzzleHttp\Client;

/**
 * Class BitapsPaymentForwardingApi.
 */
final class BitapsPaymentForwardingApi
{
    private Client $client;
    private string $url;

    public function __construct(CryptoCurrency $cryptoCurrency)
    {
        $this->url = "https://api.bitaps.com/{$cryptoCurrency->getValue()}/v1/";

        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * @param $forwarding_address
     * @param null       $callback_link
     * @param int        $confirmations
     * @param Proxy|null $proxy
     *
     * @return mixed
     */
    public function createForwardingAddress($forwarding_address, $callback_link = null, $confirmations = null, ?Proxy $proxy = null)
    {
        $confirmations = $confirmations ?? 3;

        $response = $this->client->post(
            $this->url.'/create/payment/address',
            [
                'json'    => compact('forwarding_address', 'callback_link', 'confirmations'),
                'proxy'   => $proxy ? $proxy->toString() : null,
                'timeout' => 10,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $decodedResponse = json_decode($response->getBody()->getContents(), true);

        if ($decodedResponse['error_code'] ?? null) {
            throw new BitapsApiNotAvailableException(json_encode($decodedResponse));
        }

        return $decodedResponse;
    }
}

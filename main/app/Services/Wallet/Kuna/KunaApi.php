<?php

declare(strict_types=1);

namespace App\Services\Wallet\Kuna;

use App\Models\Proxy;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeActivationErrorException;
use GuzzleHttp\Client;
use Log;

/**
 * Class KunaApi.
 */
final class KunaApi
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
            'headers'     => [
                'accept'       => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function checkCode(string $code, ?Proxy $proxy = null): array
    {
        $checkingPart = mb_substr($code, 0, 5);

        return \GuzzleHttp\json_decode(
            $this->client->get(
                "https://api.kuna.io/v3/kuna_codes/$checkingPart/check",
                [
                    'proxy' => $proxy ? $proxy->toString() : null,
                ]
            )->getBody()->getContents(),
            true
        );
    }

    public function activateCode(string $code, string $publicKey, string $privateKey, ?Proxy $proxy = null): array
    {
        $apiPath = '/v3/auth/kuna_codes/redeem';

        $time = now()->getPreciseTimestamp(3);

        $bodyArray = [
            'code' => $code,
        ];

        $body = \GuzzleHttp\json_encode($bodyArray);

        $sign = hash_hmac(
            'sha384',
            "{$apiPath}{$time}{$body}",
            $privateKey
        );

        $response = $this->client->put(
            "https://api.kuna.io$apiPath",
            [
                'headers' => [
                    'kun-nonce'     => $time,
                    'kun-apikey'    => $publicKey,
                    'kun-signature' => $sign,
                ],
                'json'  => $bodyArray,
                'proxy' => $proxy ? $proxy->toString() : null,
            ],
        );

        if ($response->getStatusCode() !== 200) {
            Log::info(
                json_encode([
                    $response->getStatusCode(),
                    $response->getBody()->getContents(),
                ], JSON_PRETTY_PRINT)
            );

            throw new KunaCodeActivationErrorException();
        }

        return \GuzzleHttp\json_decode(
            $response->getBody()->getContents(),
            true
        );
    }
}

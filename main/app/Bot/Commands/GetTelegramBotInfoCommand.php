<?php

declare(strict_types=1);

namespace App\Bot\Commands;

use GuzzleHttp\Client;

/**
 * Class GetTelegramBotInfoCommand.
 */
class GetTelegramBotInfoCommand
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    public function execute(string $botToken): array
    {
        $url = 'https://api.telegram.org/bot'
            .$botToken
            .'/getMe';

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}

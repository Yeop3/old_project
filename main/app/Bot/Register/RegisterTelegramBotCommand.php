<?php

declare(strict_types=1);

namespace App\Bot\Register;

use GuzzleHttp\Client;

/**
 * Class RegisterTelegramBotCommand.
 */
class RegisterTelegramBotCommand
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    public function execute(string $botToken, string $webHookUrl): array
    {
        $url = 'https://api.telegram.org/bot'
            .$botToken
            .'/setWebhook'
            .'?url='
            .$webHookUrl;

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}

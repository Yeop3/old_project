<?php

declare(strict_types=1);

namespace App\Bot\Delete;

use GuzzleHttp\Client;

/**
 * Class DeleteTelegramBotCommand.
 */
class DeleteTelegramBotCommand
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * DeleteTelegramBotCommand constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * @param string $botToken
     *
     * @return array
     */
    public function execute(string $botToken): array
    {
        $url = 'https://api.telegram.org/bot'
            .$botToken
            .'/deleteWebhook';

        return json_decode($this->client->get($url)->getBody()->getContents(), true);
    }
}

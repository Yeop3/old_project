<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Client;
use BotMan\BotMan\Interfaces\UserInterface;

/**
 * Class ClientResolver.
 */
final class ClientResolver
{
    private bool    $resolved = false;
    private ?Client $client = null;

    /**
     * @param UserInterface $user
     * @param string        $driverName
     * @param int           $sellerId
     *
     * @return Client
     */
    public function resolve(UserInterface $user, string $driverName, int $sellerId): Client
    {
        if ($this->resolved) {
            return $this->client;
        }

        $id = $user->getId();

        $this->client = Client::whereSellerId($sellerId)->whereTelegramId($id)->first();
        $this->resolved = true;

        return $this->client;
    }
}

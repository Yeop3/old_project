<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\Conversations\CreateProductTypeConversation;
use App\Models\Bot;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;

/**
 * Class CreateProductTypeHandler.
 */
final class CreateProductTypeHandler implements BotHandler
{
    private ClientResolver $clientResolver;

    public function __construct(ClientResolver $clientResolver)
    {
        $this->clientResolver = $clientResolver;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        if (!$client) {
            return;
        }

        $driver = $botModel->drivers()->where('client_id', $client->id)->first();

        if ($driver->canCreateProduct()) {
            $botMan->startConversation(new CreateProductTypeConversation($client->seller), $botMan->getUser()->getId());
        }
    }
}

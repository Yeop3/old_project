<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\Conversations\CreateProductConversation;
use App\Models\Bot;
use App\Services\Client\ClientResolver;
use App\Services\Product\Create\CreateProductCommand;
use App\Services\Product\ProductDtoSetter;
use BotMan\BotMan\BotMan;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class CreateProductHandler.
 */
final class CreateProductHandler implements BotHandler
{
    private ClientResolver $clientResolver;

    public function __construct(ClientResolver $clientResolver)
    {
        $this->clientResolver = $clientResolver;
    }

    /**
     * @param BotMan $botMan
     * @param Bot $botModel
     * @param string|null ...$params
     * @throws BindingResolutionException
     */
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
        $dto = new ProductDtoSetter();
        if ($driver->canCreateProduct()) {
            $botMan->startConversation(
                new CreateProductConversation(
                    $client->seller,
                    $botModel,
                    $driver,
                    $dto
                ),
                $botMan->getUser()->getId()
            );
        }
    }
}

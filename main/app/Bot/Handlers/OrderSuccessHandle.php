<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\Conversations\OrderConversation;
use App\Models\Bot;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;

/**
 * Class OrderSuccessHandle.
 */
final class OrderSuccessHandle implements BotHandler
{
    private ClientResolver $clientResolver;

    /**
     * OrderSuccessHandle constructor.
     *
     * @param ClientResolver $clientResolver
     */
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

        $order = $client->lastOrder;

        if (!$order) {
            return;
        }

        if ($botMan->getMessage()->getText() === 'Наход') {
            $order->found = 1;
            $order->not_found = 0;
            $order->save();
            $botMan->startConversation(new OrderConversation($order), $botMan->getUser()->getId());
        } else {
            $order->found = 0;
            $order->not_found = 1;
            $order->save();
            $botMan->startConversation(new OrderConversation($order), $botMan->getUser()->getId());
        }
    }
}

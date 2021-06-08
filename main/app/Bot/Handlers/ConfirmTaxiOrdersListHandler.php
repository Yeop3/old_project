<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;

/**
 * Class ConfirmTaxiOrdersListHandler.
 */
final class ConfirmTaxiOrdersListHandler implements BotHandler
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

        if ($client->deliveryOrders->count() === 0) {
            return;
        }

        $ordersTexts = $client->deliveryOrders->map(function (Order $order): string {
            return "<b>Заказ {$order->getFullText()}</b>\n"
                ."В районе <b>{$order->product->location->name_chain}</b>\n"
                ."Адрес: {$order->product->delivery_address}\n"
                ."Подтвердить, что заказ доставлен - /order_delivery_confirm_{$order->number}";
        });

        $botMan->reply(
            $ordersTexts->implode("\n- - - - - - - - - - - - - - - -\n"),
            ['parse_mode' => 'HTML']
        );
    }
}

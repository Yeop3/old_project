<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Driver;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use BotMan\BotMan\BotMan;

/**
 * Class DriverHotOrdersListHandler.
 */
final class DriverHotOrdersListHandler implements BotHandler
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

        /** @var Driver $driver */
        $driver = $client->driver;

        if (!$driver || !$driver->bots()->where('id', $botModel->id)->exists()) {
            return;
        }

        if (!$driver->canProcessHotOrder()) {
            return;
        }

        $orders = Order::whereSellerId($client->seller_id)
            ->whereHas(
                'product',
                fn ($builder) => $builder
                    ->where('delivery_type', DeliveryType::HOT_TREASURE)
                    ->where('client_telegram_id', $driver->telegram_id)
                    ->where('status', ProductStatus::STATUS_SOLD)
            )
            ->get();

        if (!$orders->count()) {
            $botMan->reply('На данный момент нет горячих заказов');

            return;
        }

        $ordersTexts = $orders->map(function (Order $order): string {
            return "<b>Заказ {$order->getFullText()}</b>\n"
                ."В районе <b>{$order->product->location->name_chain}</b>\n"
                ."Обработать заказ - /order_hot_process_{$order->number}";
        });

        $botMan->reply(
            $ordersTexts->implode("\n- - - - - - - - - - - - - - - -\n"),
            ['parse_mode' => 'HTML']
        );
    }
}

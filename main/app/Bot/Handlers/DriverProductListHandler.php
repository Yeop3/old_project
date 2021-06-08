<?php

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Driver;
use App\Models\Product;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;

/**
 * Class DriverProductListHandler.
 */
class DriverProductListHandler implements BotHandler
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

        $products = Product::whereSellerId($client->seller_id)
            ->where('client_telegram_id', $client->telegram_id)
            ->get();

        if (!$products->count()) {
            $botMan->reply('У вас пока нет кладов');

            return;
        }

        $products->each(function (Product $product) use ($botMan) {
            $botMan->reply(
                "Товар: {$product->productType->getFullName()}\n"
                ."В районе: <b>{$product->location->name_chain}</b>\n"
                ."Дата создания: {$product->created_at}\n"
                ."Статус: {$product->status_name}\n"
                ."Фото: {$product->photos->count()}"
            );
        });
    }
}

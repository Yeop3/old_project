<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\Conversations\ProcessTaxiOrderConversation;
use App\Models\Bot;
use App\Models\Driver;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Product\Checker;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use BotMan\BotMan\BotMan;

/**
 * Class DriverProcessTaxiOrderHandler.
 */
final class DriverProcessTaxiOrderHandler implements BotHandler
{
    private ClientResolver $clientResolver;
    private Checker        $checker;

    /**
     * DriverProcessTaxiOrderHandler constructor.
     *
     * @param ClientResolver $clientResolver
     * @param Checker        $checker
     */
    public function __construct(ClientResolver $clientResolver, Checker $checker)
    {
        $this->clientResolver = $clientResolver;
        $this->checker = $checker;
    }

    /**
     * @param BotMan      $botMan
     * @param Bot         $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $orderNumber = $params[0] ?? null;

        if (!$orderNumber) {
            return;
        }

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

        if (!$driver->canProcessTaxiOrder()) {
            return;
        }

        $order = Order::whereSellerId($client->seller_id)
            ->whereNumber($orderNumber)
            ->whereHas(
                'product',
                fn ($builder) => $builder
                    ->where('delivery_type', DeliveryType::TAXI)
                    ->where('client_telegram_id', $driver->telegram_id)
                    ->where('status', ProductStatus::STATUS_SOLD)
            )
            ->first();

        if (!$order) {
            return;
        }

        $botMan->reply(
            "<b>Вы выбрали заказ {$order->getFullText()}</b>\n"
            ."В районе <b>{$order->product->location->name_chain}</b>\n"
            ."Адрес: {$order->product->delivery_address}",
            ['parse_mode' => 'HTML']
        );

        $botMan->startConversation(
            new ProcessTaxiOrderConversation($this->checker, $order, $driver),
            $botMan->getUser()->getId()
        );
    }
}

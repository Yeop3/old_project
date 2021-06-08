<?php

declare(strict_types=1);

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\Order;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyDriverTaxiOrder.
 */
final class NotifyDriverTaxiOrder
{
    /**
     * @param Order $order
     *
     * @throws Exception
     */
    public function execute(Order $order): void
    {
        if (!$order->client) {
            return;
        }

        if (!$order->product->productType->delivery_type->isTaxi()) {
            return;
        }

        if ($order->client->source_type === SourceType::TYPE_SITE) {
            throw new RuntimeException('source type "site" not yet realized');
        }

        $botModel = \App\Models\Bot::find($order->source_id);

        $bot = new Bot($botModel);

        $driver = $order->product->driver;

        $productText = $order->getProductText();

        $message = "<b>Новый заказ на доставку!</b>\n$productText\n";
        $message .= "В районе <b>{$order->product->location->name_chain}</b>\n";
        $message .= "По адресу: {$order->product->delivery_address}";

        $bot->say(
            $message,
            [$driver->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

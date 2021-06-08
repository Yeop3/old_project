<?php

declare(strict_types=1);

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Bot\Conversations\ConfirmHotOrderConversation;
use App\Models\Order;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyDriverHotOrder.
 */
final class NotifyDriverHotOrder
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

        $message = "<b>Новый горячий заказ!</b>\n$productText\n";
        $message .= "В районе <b>{$order->product->location->name_chain}</b>";

        $bot->say(
            $message,
            [$driver->client->telegram_id],
            $botModel->getBotDriver()
        );

        $bot->getBotman()
            ->startConversation(
                new ConfirmHotOrderConversation($order),
                $driver->client->telegram_id,
                $botModel->getBotDriver()
            );
    }
}

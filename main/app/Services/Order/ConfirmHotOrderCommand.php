<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Bot;
use App\Models\Order;
use App\Services\Order\VO\OrderStatus;
use App\VO\SourceType;
use RuntimeException;

/**
 * Class ConfirmHotOrderCommand.
 */
final class ConfirmHotOrderCommand
{
    public function execute(Order $order): void
    {
        if ($order->client->source_type === SourceType::TYPE_SITE) {
            throw new RuntimeException('source type "site" not yet realized');
        }

        if (!$order->productMaybeDeleted) {
            return;
        }

        $allowedStatuses = [
            OrderStatus::STATUS_GIVEN,
            OrderStatus::STATUS_PAID,
        ];

        if (!in_array($order->status->getValue(), $allowedStatuses, true)) {
            return;
        }

        $order->status = new OrderStatus(OrderStatus::STATUS_IN_DELIVERY);

        $order->save();

        $botModel = Bot::find($order->source_id);

        $bot = new \App\Bot\Bot($botModel);

        $text = "<b>В скором времени курьер сделает для вас клад.</b>\n"
        ."{$order->getFullText()}\n"
        ."В районе <b>{$order->product->location->name_chain}</b>\n"
        .'Ожидайте уведомления.';

        $bot->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\BotLogic\BotLogicEvent;
use App\Models\Order;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyOrderCanceledByTimeout.
 */
final class NotifyOrderCanceledByTimeout
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

        if ($order->client->source_type === SourceType::TYPE_SITE) {
            throw new RuntimeException('source type "site" not yet realized');
        }

        $botModel = \App\Models\Bot::find($order->source_id);

        $bot = new Bot($botModel);

        $botLogic = $botModel->logic;

        $event = BotLogicEvent::whereBotLogicId($botLogic->id)
            ->where('key', 'order_cancel_by_timeout')
            ->firstOrFail();

        $bot->say(
            $event->content,
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

<?php

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\BotLogic\BotLogicOperatorNotification;
use App\Models\Order;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyOrderCanceledByOperator.
 */
final class NotifyOrderCanceledByOperator
{
    /**
     * @param Order $order
     *
     * @throws Exception
     */
    public function execute(Order $order): void
    {
        if ($order->client->source_type === SourceType::TYPE_SITE) {
            throw new RuntimeException('source type "site" not yet realized');
        }

        $botModel = \App\Models\Bot::find($order->source_id);

        $bot = new Bot($botModel);

        $botLogic = $botModel->logic;

        $event = BotLogicOperatorNotification::whereBotLogicId($botLogic->id)
            ->where('key', 'cancel')
            ->firstOrFail();

        $bot->say(
            $event->content,
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

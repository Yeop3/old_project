<?php

declare(strict_types=1);

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\BotLogic\BotLogicEvent;
use App\Models\Order;
use App\Models\Wallet\Wallet;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyOrderPartiallyPaid.
 */
final class NotifyOrderPartiallyPaid
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

        $event = BotLogicEvent::whereBotLogicId($botLogic->id)
            ->where('key', $order->payment_method->getEventKey('partially_paid'))
            ->firstOrFail();

        $paidAmount = $order->getPaidAmount();

        $cryptoUnpaid = getOrderCryptoUnpaidText($order);

        $cryptoPaid = getOrderCryptoPaidText($order, $paidAmount);

        $message = str_replace(
            [
                '{order-number}',
                '{order-amount-paid}',
                '{order-amount-unpaid}',
                '{order-crypto-amount-paid}',
                '{order-crypto-amount-unpaid}',
                '{order-crypto-address}',
            ],
            [
                $order->number,
                formatMoney($order->total_price->subtract($order->unpaid_amount)).' грн',
                formatMoney($order->unpaid_amount).' грн',
                $cryptoPaid,
                $cryptoUnpaid,
                $order->getCryptoAddress(),
            ],
            $event->content
        );

        /** @var Wallet $wallet */
        $wallet = $order->wallet;

        $message = $wallet->replaceTextVars($message);

        $bot->say(
            $message,
            [$order->client->telegram_id],
            $botModel->getBotDriver(),
        );
    }
}

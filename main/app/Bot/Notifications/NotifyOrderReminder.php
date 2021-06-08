<?php

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\BotLogic\BotLogicReminder;
use App\Models\Order;
use App\Services\Order\VO\OrderStatus;
use App\VO\SourceType;
use Exception;
use RuntimeException;

/**
 * Class NotifyOrderReminder.
 */
final class NotifyOrderReminder
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
        $event = '';
        if ($order->status->getValue() === OrderStatus::STATUS_AWAITING_PAYMENT) {
            $event = BotLogicReminder::where('key', 'payment')
                ->whereBotLogicId($botLogic->id)
                ->firstOrFail();
        } else {
            $event = BotLogicReminder::where('key', 'payment_partially')
                ->whereBotLogicId($botLogic->id)
                ->firstOrFail();
        }

        $options = collect($event->options);

        if ($order->status->getValue() === OrderStatus::STATUS_AWAITING_PAYMENT) {
            $eventText = $options->first(
                fn ($value) => $value['key'] === $order->payment_method->getReminderKey('payment')
            )['value'];
        } else {
            $eventText = $options->first(
                fn ($value) => $value['key'] === $order->payment_method->getReminderKey('partially_paid')
            )['value'];
        }

        $paidAmount = $order->getPaidAmount();

        $cryptoUnpaid = getOrderCryptoUnpaidText($order);

        $cryptoPaid = getOrderCryptoPaidText($order, $paidAmount);

        $eventText = str_replace([
            '{order-product-product_type-name}',
            '{order-amount-paid}',
            '{order-amount-unpaid}',
            '{order-number}',
            '{order-purse-phone}',
            '{order-price}',
            '{order-product-location}',
            '{order-reserve-left}',
            '{order-crypto-amount-paid}',
            '{order-crypto-amount-unpaid}',
            '{order-crypto-address}',
        ], [
            $order->product->productType->name,
            formatMoney($order->total_price->subtract($order->unpaid_amount)).' грн',
            formatMoney($order->unpaid_amount).' грн',
            '#'.$order->number,
            $order->wallet->phone,
            formatMoney($order->total_price).' грн',
            $order->product->location->name,
            $order->getReservationTimeLeft(),
            $cryptoPaid,
            $cryptoUnpaid,
            $order->getCryptoAddress(),
        ], $eventText);

        $eventText = $order->wallet->replaceTextVars($eventText);

        $bot->say(
            $eventText,
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

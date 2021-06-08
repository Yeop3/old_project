<?php

declare(strict_types=1);

namespace App\Bot\Notifications;

use App\Bot\Commands\OrderSuccessSendMessage;
use App\Events\StokerProductTypeNotifyEvent;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicEvent;
use App\Models\Order;
use App\Models\Product;
use App\Services\Order\VO\OrderStatus;
use App\VO\SourceType;
use BotMan\BotMan\Exceptions\Base\BotManException;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class NotifyOrderPaid.
 */
final class NotifyOrderPaid
{
    private OrderSuccessSendMessage $orderSuccessSendMessage;
    private NotifyDriverTaxiOrder   $notifyDriverTaxiOrder;
    private NotifyDriverHotOrder    $notifyDriverHotOrder;

    /**
     * NotifyOrderPaid constructor.
     *
     * @param OrderSuccessSendMessage $orderSuccessSendMessage
     * @param NotifyDriverTaxiOrder   $notifyDriverTaxiOrder
     * @param NotifyDriverHotOrder    $notifyDriverHotOrder
     */
    public function __construct(
        OrderSuccessSendMessage $orderSuccessSendMessage,
        NotifyDriverTaxiOrder $notifyDriverTaxiOrder,
        NotifyDriverHotOrder $notifyDriverHotOrder
    ) {
        $this->orderSuccessSendMessage = $orderSuccessSendMessage;
        $this->notifyDriverTaxiOrder = $notifyDriverTaxiOrder;
        $this->notifyDriverHotOrder = $notifyDriverHotOrder;
    }

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

        if (!$order->productMaybeDeleted) {
            return;
        }

        $allowedStatuses = [
            OrderStatus::STATUS_GIVEN,
            OrderStatus::STATUS_PAID,
        ];

        if (!in_array($order->status->getValue(), $allowedStatuses, true)) {
            Log::info('wrong order paid notification', $order->toArray());

            return;
        }

        $botModel = Bot::find($order->source_id);

        $botLogic = $botModel->logic;

        $event = BotLogicEvent::whereBotLogicId($botLogic->id)
            ->where('key', 'order_paid')
            ->firstOrFail();

        $product = $order->productMaybeDeleted;

        $bot = new \App\Bot\Bot($botModel);

        if ($product->delivery_type->isTaxi()) {
            $this->handleTaxiProduct($bot, $order, $botModel);

            return;
        }

        if ($product->delivery_type->isHotTreasure()) {
            $this->handleHotTreasureProduct($bot, $order, $botModel);

            return;
        }

        $this->handleTreasureProduct($product, $event, $order, $botModel, $bot);
    }

    /**
     * @param \App\Bot\Bot $bot
     * @param Order        $order
     * @param $botModel
     *
     * @throws Exception
     */
    private function handleTaxiProduct(\App\Bot\Bot $bot, Order $order, $botModel): void
    {
        $message = "<b>Вы оплатили товар</b>\nВ скором времени курьер доставит вам его.\nОжидайте уведомления.";
        $bot->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $message),
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );

        $this->notifyDriverTaxiOrder->execute($order);
    }

    /**
     * @param \App\Bot\Bot $bot
     * @param Order        $order
     * @param $botModel
     *
     * @throws BotManException
     * @throws Exception
     */
    private function handleHotTreasureProduct(\App\Bot\Bot $bot, Order $order, $botModel): void
    {
        $message = "<b>Вы оплатили товар</b>\nОжидайте подтверждения от курьера.";
        $bot->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $message),
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );

        $this->notifyDriverHotOrder->execute($order);
    }

    /**
     * @param Product            $product
     * @param BotLogicEvent|null $event
     * @param Order              $order
     * @param $botModel
     * @param \App\Bot\Bot $bot
     *
     * @throws BotManException
     */
    private function handleTreasureProduct(
        Product $product,
        BotLogicEvent $event,
        Order $order,
        $botModel,
        \App\Bot\Bot $bot
    ): void {
        $content = str_replace(
            [
                '{order-product-content}',
                '{order-product-coordinates}',
            ],
            [
                $product ? $product->getAddressForBot() : '',
                $product->coordinates,
            ],
            $event->content
        );

        event(new StokerProductTypeNotifyEvent($order->product));

        $this->orderSuccessSendMessage->execute($order, $botModel, $content);

        $bot->saySuccessOrder(
            'Известите, пожалуйста, нас об находе/не находе.',
            [$order->client->telegram_id],
            $botModel->getBotDriver()
        );
    }
}

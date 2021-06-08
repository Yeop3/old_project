<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Bot\Commands\OrderSuccessSendMessage;
use App\Events\SellerBotMessageSent;
use App\Models\Order;
use App\Models\ProductPhoto;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class SendHotProductCommand.
 */
final class SendHotProductCommand
{
    private Dispatcher $dispatcher;
    private OrderSuccessSendMessage $orderSuccessSendMessage;

    public function __construct(OrderSuccessSendMessage $orderSuccessSendMessage, Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->orderSuccessSendMessage = $orderSuccessSendMessage;
    }

    public function execute(Order $order): void
    {
        $order->product->status = new ProductStatus(ProductStatus::STATUS_DELIVERED);
        $order->product->delivery_started_at = now();

        $order->status = new OrderStatus(OrderStatus::STATUS_DELIVERED);

        DB::beginTransaction();

        $order->product->save();
        $order->save();

        $content = "<b>Ваш клад готов!</b>\n";
        $content .= $order->getFullText();

        if ($order->product->coordinates) {
            $content .= "\n➖➖➖➖➖➖➖➖➖\n";
            $content .= "Заберите его по координатам {$order->product->coordinates}";
            $content .= "\n➖➖➖➖➖➖➖➖➖\n";
        }

        if ($order->product->address) {
            $content .= "\n➖➖➖➖➖➖➖➖➖\n";
            $content .= "Описание: {$order->product->address}";
            $content .= "\n➖➖➖➖➖➖➖➖➖\n";
        }

        $this->orderSuccessSendMessage->execute($order, $order->source, $content);

        DB::commit();

        try {
            $this->dispatcher->dispatch(
                new SellerBotMessageSent(
                    $order->client,
                    new MessageFromBotDto(
                        $content,
                        $order->productMaybeDeleted->photos->map(
                            fn (ProductPhoto $productPhoto) => $productPhoto->url
                        )->toArray(),
                        $order->productMaybeDeleted->video_url ? [$order->productMaybeDeleted->video_url] : [],
                        $order->productMaybeDeleted->coordinates
                            ? $order->productMaybeDeleted->coordinates->getValue()
                            : null,
                    ),
                )
            );
        } catch (Exception $e) {
            return;
        }
    }
}

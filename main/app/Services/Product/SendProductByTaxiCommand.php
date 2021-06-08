<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Bot\Commands\SendOrderPhotos;
use App\Events\SellerBotMessageSent;
use App\Models\Bot;
use App\Models\Order;
use App\Models\ProductPhoto;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class SendProductByTaxiCommand.
 */
final class SendProductByTaxiCommand
{
    private SendOrderPhotos $sendOrderPhotos;
    private Dispatcher $dispatcher;

    public function __construct(SendOrderPhotos $sendOrderPhotos, Dispatcher $dispatcher)
    {
        $this->sendOrderPhotos = $sendOrderPhotos;
        $this->dispatcher = $dispatcher;
    }

    public function execute(Order $order): void
    {
        $order->product->status = new ProductStatus(ProductStatus::STATUS_IN_DELIVERY);
        $order->product->delivery_started_at = now();

        $order->status = new OrderStatus(OrderStatus::STATUS_IN_DELIVERY);

        DB::beginTransaction();

        $order->product->save();
        $order->save();

        $botModel = Bot::find($order->source_id);

        $text = "<b>Ваш заказ отправлен</b>\n"
            ."{$order->getFullText()}\n"
            ."В районе <b>{$order->product->location->name_chain}</b>\n"
            ."На адрес: {$order->product->delivery_address}";

        if ($order->product->address && $order->product->delivery_address !== $order->product->address) {
            $text .= "\n{$order->product->address}";
        }

        $this->sendOrderPhotos->execute(new Client(), $botModel, $order, $text);

        DB::commit();

        try {
            $this->dispatcher->dispatch(
                new SellerBotMessageSent(
                    $order->client,
                    new MessageFromBotDto(
                        $text,
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

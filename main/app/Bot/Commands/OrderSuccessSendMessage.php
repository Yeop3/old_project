<?php

declare(strict_types=1);

namespace App\Bot\Commands;

use App\Events\SellerBotMessageSent;
use App\Models\Bot;
use App\Models\Order;
use App\Models\ProductPhoto;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use GuzzleHttp\Client;
use Illuminate\Events\Dispatcher;
use Throwable;

/**
 * Class OrderSuccessSendMessage.
 */
final class OrderSuccessSendMessage
{
    private Dispatcher      $dispatcher;
    private SendOrderPhotos $sendOrderPhotos;

    /**
     * OrderSuccessSendMessage constructor.
     *
     * @param Dispatcher      $dispatcher
     * @param SendOrderPhotos $sendOrderPhotos
     */
    public function __construct(Dispatcher $dispatcher, SendOrderPhotos $sendOrderPhotos)
    {
        $this->dispatcher = $dispatcher;
        $this->sendOrderPhotos = $sendOrderPhotos;
    }

    /**
     * @param Order $order
     * @param Bot $botModel
     * @param string $content
     * @throws Throwable
     */
    public function execute(Order $order, Bot $botModel, string $content): void
    {
        $guzzleClient = new Client();

        if (!($orderProduct = $order->productMaybeDeleted)) {
            return;
        }

        if ($orderProduct->image_src) {
            $oldPhoto = new ProductPhoto();
            $oldPhoto->src = $orderProduct->image_src;

            $orderProduct->photos->prepend($oldPhoto);
        }

        if ($orderProduct->video_src && $orderProduct->photos->count()) {
            $this->sendAll($guzzleClient, $botModel, $order, $content);

            return;
        }

        if (!$orderProduct->video_src && !$orderProduct->photos->count()) {
            $this->sendLocation($order, $guzzleClient, $botModel);

            $this->dispatcher->dispatch(
                new SellerBotMessageSent(
                    $order->client,
                    new MessageFromBotDto(
                        '',
                        [],
                        [],
                        $orderProduct->coordinates
                            ? $orderProduct->coordinates->getValue()
                            : null
                    ),
                )
            );

            return;
        }

        if ($orderProduct->photos->count()) {
            $this->sendOrderPhotos->execute($guzzleClient, $botModel, $order, $content);
        } elseif ($orderProduct->video_src) {
            $this->sendVideo($guzzleClient, $botModel, $order, $content);
        }

        $this->sendLocation($order, $guzzleClient, $botModel);

        $this->dispatcher->dispatch(
            new SellerBotMessageSent(
                $order->client,
                new MessageFromBotDto(
                    $content,
                    $orderProduct->photos->map(fn (ProductPhoto $productPhoto) => $productPhoto->url)->toArray(),
                    $orderProduct->video_url ? [$orderProduct->video_url] : [],
                    $orderProduct->coordinates
                        ? $orderProduct->coordinates->getValue()
                        : null
                ),
            )
        );
    }

    /**
     * @param Client $guzzleClient
     * @param Bot    $botModel
     * @param Order  $order
     * @param string $content
     */
    private function sendAll(Client $guzzleClient, Bot $botModel, Order $order, string $content): void
    {
        $guzzleClient
            ->post("https://api.telegram.org/bot{$botModel->token}/sendMessage", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $order->client->telegram_id,
                    ],
                    [
                        'name'     => 'text',
                        'contents' => $content,
                    ],
                    [
                        'name'     => 'parse_mode',
                        'contents' => 'HTML',
                    ],
                ],
            ]);

        $photosFiles = $order->productMaybeDeleted->photos->map(
            fn (ProductPhoto $photo, $k) => [
                'name'     => 'photo_'.$k,
                'contents' => fopen(storage_path('app/public/'.$photo->src), 'rb'),
            ]
        )->toArray();

        $photosMedias = $order->productMaybeDeleted->photos->map(
            fn (ProductPhoto $photo, $k) => [
                'type'  => 'photo',
                'media' => 'attach://photo_'.$k,
            ]
        )->toArray();

        $guzzleClient
            ->post("https://api.telegram.org/bot{$botModel->token}/sendMediaGroup", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $order->client->telegram_id,
                    ],
                    ...$photosFiles,
                    [
                        'name'     => 'video',
                        'contents' => fopen(
                            storage_path('app/public/'.$order->productMaybeDeleted->video_src),
                            'rb'
                        ),
                    ],
                    [
                        'name'     => 'media',
                        'contents' => json_encode([
                            ...$photosMedias,
                            [
                                'type'  => 'video',
                                'media' => 'attach://video',
                            ],
                        ]),
                    ],
                ],
            ]);
//        } catch (JsonException $e) {
//            Log::error("", "", "")
//        }

        $this->sendLocation($order, $guzzleClient, $botModel);

        $this->dispatcher->dispatch(
            new SellerBotMessageSent(
                $order->client,
                new MessageFromBotDto(
                    $content,
                    $order->productMaybeDeleted->photos->map(fn (
                        ProductPhoto $productPhoto
                    ) => $productPhoto->url)->toArray(),
                    [$order->productMaybeDeleted->video_url],
                    $order->productMaybeDeleted->coordinates
                        ? $order->productMaybeDeleted->coordinates->getValue()
                        : null
                ),
            )
        );
    }

    private function sendLocation(Order $order, Client $guzzleClient, Bot $botModel): void
    {
        if (!$order->productMaybeDeleted->coordinates) {
            return;
        }

        [$lat, $lng] = $order->productMaybeDeleted->coordinates->getLatLng();

        if (!$lat || !$lng) {
            return;
        }

        $guzzleClient
            ->post("https://api.telegram.org/bot{$botModel->token}/sendLocation", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $order->client->telegram_id,
                    ],
                    [
                        'name'     => 'latitude',
                        'contents' => trim($lat),
                    ],
                    [
                        'name'     => 'longitude',
                        'contents' => trim($lng),
                    ],
                ],
            ]);
    }

    private function sendVideo(Client $guzzleClient, Bot $botModel, Order $order, string $content): void
    {
        $guzzleClient
            ->post("https://api.telegram.org/bot{$botModel->token}/sendVideo", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $order->client->telegram_id,
                    ],
                    [
                        'name'     => 'caption',
                        'contents' => $content,
                    ],
                    [
                        'name'     => 'parse_mode',
                        'contents' => 'HTML',
                    ],
                    [
                        'name'     => 'video',
                        'contents' => fopen(storage_path('app/public/'.$order->productMaybeDeleted->video_src), 'rb'),
                    ],
                ],
            ]);
    }
}

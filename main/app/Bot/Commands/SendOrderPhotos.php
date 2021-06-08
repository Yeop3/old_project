<?php

declare(strict_types=1);

namespace App\Bot\Commands;

use App\Models\Bot;
use App\Models\Order;
use App\Models\ProductPhoto;
use GuzzleHttp\Client;
use Throwable;


/**
 * Class SendOrderPhotos.
 */
final class SendOrderPhotos
{

    /**
     * @param Client $guzzleClient
     * @param Bot $botModel
     * @param Order $order
     * @param string $content
     * @throws Throwable
     */
    public function execute(Client $guzzleClient, Bot $botModel, Order $order, string $content): void
    {
        if ($order->productMaybeDeleted->photos->count() === 1) {
            $guzzleClient
                ->post(
                    "https://api.telegram.org/bot{$botModel->token}/sendPhoto",
                    [
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
                                'name'     => 'photo',
                                'contents' => fopen(
                                    storage_path(
                                        'app/public/'.$order->productMaybeDeleted
                                            ->photos
                                            ->first()
                                            ->src
                                    ),
                                    'rb'
                                ),
                            ],
                        ],
                ]
                );

            return;
        }

        if ($content) {
            //TODO: Remove duplicate @link OrderSuccess

            $guzzleClient
                ->post(
                    "https://api.telegram.org/bot{$botModel->token}/sendMessage",
                    [
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
                ]
                );
        }

        $photosFiles = $order->productMaybeDeleted
            ->photos
            ->map(
                fn (ProductPhoto $photo, $k) => [
                    'name'     => 'photo_'.$k,
                    'contents' => fopen(
                        storage_path(
                            'app/public/'.$photo->src
                        ),
                        'rb'
                    ),
                ]
            )->toArray();

        $photosMedias = $order->productMaybeDeleted
            ->photos
            ->map(
                fn (ProductPhoto $photo, $k) => [
                    'type'  => 'photo',
                    'media' => 'attach://photo_'.$k,
                ]
            )->toArray();

        $guzzleClient
            ->post(
                "https://api.telegram.org/bot{$botModel->token}/sendMediaGroup",
                [
                    'multipart' => [
                        [
                            'name'     => 'chat_id',
                            'contents' => $order->client->telegram_id,
                        ],
                        ...$photosFiles,
                        [
                            'name'     => 'media',
                            'contents' => json_encode([
                                ...$photosMedias,
                            ], JSON_THROW_ON_ERROR),
                        ],
                    ],
            ]
            );
    }
}

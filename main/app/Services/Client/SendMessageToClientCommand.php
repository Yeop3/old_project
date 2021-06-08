<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Events\SellerBotMessageSent;
use App\Models\Bot;
use App\Models\Client;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

/**
 * Class SendMessageToClientCommand.
 */
class SendMessageToClientCommand
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Client     $client
     * @param Bot        $botModel
     * @param string     $message
     * @param array|null $photos
     */
    public function execute(Client $client, Bot $botModel, string $message, ?array $photos): void
    {
        $httpClient = new Guzzle();

        $photosFiles = [];
        $photosMedias = [];
        $messageToDB = $message;

        $httpClient
            ->post("https://api.telegram.org/bot{$botModel->token}/sendMessage", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $client->telegram_id,
                    ],
                    [
                        'name'     => 'text',
                        'contents' => $message,
                    ],
                    [
                        'name'     => 'parse_mode',
                        'contents' => 'HTML',
                    ],
                ],
            ]);

        $photos = collect($photos)->transform(fn ($value, &$key) => ++$key);

        if ($photos->count()) {
            $photos->each(function ($photo, $key) use ($messageToDB, $client) {
                $conversionImage = Image::make($photo->getRealPath());

                $conversionImage->resize(480, 640);

                $storageInside = implode(DIRECTORY_SEPARATOR, ['app', 'public']);

                $dirPath = implode('', [DIRECTORY_SEPARATOR, 'message_img', DIRECTORY_SEPARATOR, $client->id]);

                if (!file_exists(storage_path($storageInside.$dirPath))) {
                    File::makeDirectory(storage_path($storageInside.$dirPath), 0777, true);
                }

                $path = implode(DIRECTORY_SEPARATOR, [
                    $dirPath,
                    implode('.', [
                        Str::random(16),
                        $photo->getClientOriginalExtension(),
                    ]),
                ]);

                $conversionImage->save(
                    storage_path(
                        $storageInside
                        .$path
                    )
                );
                $photosFiles[] = [
                    'name'     => "photo_$key",
                    'contents' => fopen(storage_path($storageInside.$path), 'rb'),
                ];

                $photosMedias[] = [
                    'type'  => 'photo',
                    'media' => "attach://photo_$key",
                ];

                Str::of($messageToDB)->append(PHP_EOL.
                    config('app.url').
                    Str::replaceArray('//', ['/'], Storage::url($path)));
            });

            $httpClient->post("https://api.telegram.org/bot{$botModel->token}/sendMediaGroup", [
                'multipart' => [
                    [
                        'name'     => 'chat_id',
                        'contents' => $client->telegram_id,
                    ],
                    ...$photosFiles,
                    [
                        'name'     => 'media',
                        'contents' => json_encode([
                            ...$photosMedias,
                        ]),
                    ],
                ],
            ]);
        }

        $this->dispatcher->dispatch(
            new SellerBotMessageSent(
                $client,
                new MessageFromBotDto(
                    $messageToDB,
                    [],
                    [],
                    null
                )
            )
        );
    }
}

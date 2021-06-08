<?php

namespace App\MainBot;

use App\Models\MainBot;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Http\Request;

/**
 * Class Bot.
 */
class Bot
{
    private BotMan $botman;
    private MainBot $botModel;

    public function __construct(MainBot $botModel, ?Request $request = null)
    {
        DriverManager::loadDriver(TelegramDriver::class);

        $storage = new FileStorage(storage_path('botman'));
        $config = [
            'telegram' => [
                'token' => $botModel->token,
            ],
        ];

        $this->botModel = $botModel;

        $this->botman = BotManFactory::create(
            $config,
            new LaravelCache(),
            $request,
            $storage
        );
    }

    public function webHook(): void
    {
        foreach (HandlersMapper::MAP as $command => $handlerClass) {
            $handler = app()->make($handlerClass);
            if ($command === 'ANYKEY') {
                $this->botman->fallback(function (BotMan $botMan, string ...$params) use ($handler) {
                    $handler->execute($botMan, $this->botModel);
                });

                continue;
            }

            $this->botman->hears($command, function (BotMan $botMan, string ...$params) use ($handler) {
                $handler->execute($botMan, $this->botModel, ...$params);
            });
        }

        $this->botman->listen();
    }

    public function getBotman(): BotMan
    {
        return $this->botman;
    }
}

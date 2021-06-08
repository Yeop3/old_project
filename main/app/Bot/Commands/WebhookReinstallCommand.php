<?php

declare(strict_types=1);

namespace App\Bot\Commands;

use App\Bot\Register\RegisterTelegramBotCommand;
use App\Models\Bot;

/**
 * Class WebhookReinstallCommand.
 */
class WebhookReinstallCommand
{
    private RegisterTelegramBotCommand $registerTelegramBotCommand;

    public function __construct(RegisterTelegramBotCommand $registerTelegramBotCommand)
    {
        $this->registerTelegramBotCommand = $registerTelegramBotCommand;
    }

    public function execute(int $number, int $sellerId): array
    {
        $bot = Bot::standardTelegram()
            ->whereSellerId($sellerId)
            ->whereNumber($number)
            ->firstOrFail();

        return $this->registerTelegramBotCommand->execute($bot->token, $bot->getWebHookUrl());
    }
}

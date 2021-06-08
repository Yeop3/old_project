<?php

declare(strict_types=1);

namespace App\Services\TelegramClient\BotFather;

/**
 * Class DeleteBotCommand.
 */
final class DeleteBotCommand extends BotFather
{
    public function execute(string $username): bool
    {
        $orderOfCommands = [
            '/deletebot'              => 'Choose a bot to delete',
            '@'.$username             => 'you selected',
            'Yes, I am totally sure.' => 'The bot is gone',
        ];

        $this->handle($orderOfCommands);

        return true;
    }
}

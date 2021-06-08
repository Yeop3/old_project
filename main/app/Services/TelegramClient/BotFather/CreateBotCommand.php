<?php

declare(strict_types=1);

namespace App\Services\TelegramClient\BotFather;

/**
 * Class CreateBotCommand.
 */
final class CreateBotCommand extends BotFather
{
    private string $findTokenAfterThisPhrase = 'token to access the HTTP API';

    public function execute(string $name, string $username): string
    {
        $orderOfCommands = [
            '/newbot' => 'Please choose a name',
            $name     => 'choose a username for your bot',
            $username => 'Use this token to access',
        ];

        $this->handle($orderOfCommands);

        return $this->getTokenFromMessage();
    }

    private function getTokenFromMessage(): string
    {
        $explodeLineMessage = preg_split("/((\r?\n)|(\r\n?))/", $this->lastMessage);
        foreach ($explodeLineMessage as $key => $line) {
            if (preg_match("/($this->findTokenAfterThisPhrase)/", $line)) {
                return str_replace("\n", '', $explodeLineMessage[$key + 1]);
            }
        }
    }
}

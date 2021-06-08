<?php

namespace App\Services\TelegramClient\BotFather;

use App\Services\TelegramClient\BotFather\Exceptions\GotWrongMessageException;
use App\Services\TelegramClient\TelegramClient;

/**
 * Class BotFather.
 */
class BotFather
{
    public string $lastMessage;
    private TelegramClient $telegramClient;
    public const BOT_FATHER_USERNAME = 'BotFather';

    public function __construct(TelegramClient $telegramClient)
    {
        $this->telegramClient = $telegramClient;
    }

    /**
     * @param array $orderOfCommands
     *
     * @throws GotWrongMessageException
     */
    public function handle(array $orderOfCommands): void
    {
        foreach ($orderOfCommands as $command => $expectedPartOfMessage) {
            $this->telegramClient->sendMessage(self::BOT_FATHER_USERNAME, $command);
            sleep(5);
            $this->checkIsValidLastMessage($expectedPartOfMessage);
        }

        $this->telegramClient->deleteHistory(self::BOT_FATHER_USERNAME);
    }

    /**
     * @param string $expectedPartOfMessage
     *
     * @throws GotWrongMessageException
     *
     * @return bool
     */
    public function checkIsValidLastMessage(string $expectedPartOfMessage): bool
    {
        $this->lastMessage = $this->telegramClient->getLastMessages(self::BOT_FATHER_USERNAME)[0]['message'];
        if ((strpos($this->lastMessage, $expectedPartOfMessage) !== false)) {
            return true;
        }

        throw new GotWrongMessageException('Received message does not contain the desired phrase. (Need phrase in message: '.$expectedPartOfMessage.' | Got a message: '.$this->lastMessage.')');
    }
}

<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Services\Client\Create\ClientCreateByBotDto;
use App\Services\Client\Create\HandleClientByBotCommand;
use BotMan\BotMan\BotMan;

/**
 * Class ClientHandler.
 */
final class ClientHandler implements BotHandler
{
    private HandleClientByBotCommand $handleClientByBotCommand;

    public function __construct(HandleClientByBotCommand $handleClientByBotCommand)
    {
        $this->handleClientByBotCommand = $handleClientByBotCommand;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $user = $botMan->getUser();

        $name = trim(($user->getFirstName() ?? '').' '.($user->getLastName() ?? ''));

        $dto = new ClientCreateByBotDto((int) $user->getId(), $user->getInfo(), $name, $user->getUsername());

        $this->handleClientByBotCommand->execute($botModel, $dto);
    }
}

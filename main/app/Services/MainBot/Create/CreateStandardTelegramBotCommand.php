<?php

declare(strict_types=1);

namespace App\Services\MainBot\Create;

use App\Bot\Commands\GetTelegramBotInfoCommand;
use App\Bot\Register\RegisterTelegramBotCommand;
use App\Models\MainBot;
use App\Services\Bot\Exceptions\CantRegisterTelegramBotException;
use App\Services\MainBot\Checker;
use Illuminate\Support\Str;

/**
 * Class CreateStandardTelegramBotCommand.
 */
final class CreateStandardTelegramBotCommand
{
    private GetTelegramBotInfoCommand $getTelegramBotInfoCommand;
    private RegisterTelegramBotCommand $registerTelegramBotCommand;
    private Checker $checker;

    public function __construct(RegisterTelegramBotCommand $registerTelegramBotCommand, Checker $checker, GetTelegramBotInfoCommand $getTelegramBotInfoCommand)
    {
        $this->registerTelegramBotCommand = $registerTelegramBotCommand;
        $this->checker = $checker;
        $this->getTelegramBotInfoCommand = $getTelegramBotInfoCommand;
    }

    public function execute(CreateStandardTelegramBotDto $dto): MainBot
    {
        $this->checker->checkNameDuplicate($dto->getName());

        $bot = new MainBot();

        $botInfo = $this->getTelegramBotInfoCommand->execute($dto->getToken());

        $bot->name = $dto->getName();
        $bot->username = $botInfo['result']['username'];
        $bot->token = $dto->getToken();
        $bot->slug = Str::random();
        $bot->active = $dto->isActive();

        $registerResult = $this->registerTelegramBotCommand->execute($bot->token, $bot->getWebHookUrl());

        if (!$registerResult['ok']) {
            throw new CantRegisterTelegramBotException($registerResult['description']);
        }

        $bot->save();

        return $bot;
    }
}

<?php

declare(strict_types=1);

namespace App\MainBot;

use App\Models\MainBot;
use App\Services\MainBot\Create\CreateStandardTelegramBotCommand;
use App\Services\MainBot\Create\CreateStandardTelegramBotDto;
use App\Services\TelegramClient\BotFather\CreateBotCommand;

/**
 * Class RecreateMainBotCommand.
 */
final class RecreateMainBotCommand
{
    private CreateBotCommand $createBotCommand;

    public function __construct(CreateBotCommand $createBotCommand)
    {
        $this->createBotCommand = $createBotCommand;
    }

    public function execute(MainBot $botModel): void
    {
        $newName = $this->getNewModelName($botModel);
        $token = $this->createBotCommand->execute($newName, $this->getNewModelUsername($botModel));

        app()->make(CreateStandardTelegramBotCommand::class)
            ->execute(
                new CreateStandardTelegramBotDto(
                    $newName,
                    $token,
                    true
                )
            );

        $botModel->active = false;
        $botModel->save();
    }

    private function getNewModelUsername(MainBot $botModel): string
    {
        foreach ([('_'.$botModel->id.'_bot'), '_bot', 'bot'] as $string) {
            $explodeString = explode($string, $botModel->username);
            if (count($explodeString) > 1) {
                return $explodeString[0].'_'.($botModel->id + 1).'_bot';
            }
        }
    }

    private function getNewModelName(MainBot $botModel): string
    {
        $newId = $botModel->id + 1;
        foreach ([('_'.$botModel->id)] as $string) {
            $explodeString = explode($string, $botModel->name);
            if (count($explodeString) > 1) {
                return $explodeString[0].'_'.$newId;
            }
        }

        return $botModel->name.'_'.$newId;
    }
}

<?php

namespace App\Console\Commands;

use App\Bot\Commands\GetTelegramBotInfoCommand;
use App\Bot\Delete\DeleteTelegramBotCommand;
use App\Bot\Register\RegisterTelegramBotCommand;
use App\Models\MainBot;
use App\Services\Bot\Exceptions\CantRegisterTelegramBotException;
use Illuminate\Console\Command;

/**
 * Class UpdateMainBotToken.
 */
class UpdateMainBotToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main_bot:token:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param RegisterTelegramBotCommand $registerTelegramBotCommand
     * @param DeleteTelegramBotCommand   $deleteTelegramBotCommand
     * @param GetTelegramBotInfoCommand  $getTelegramBotInfoCommand
     *
     * @return int
     */
    public function handle(RegisterTelegramBotCommand $registerTelegramBotCommand, DeleteTelegramBotCommand $deleteTelegramBotCommand, GetTelegramBotInfoCommand $getTelegramBotInfoCommand): ?int
    {
        $bot = MainBot::first();

        if (!$bot) {
            $this->error("Can't find anyone MainBot");

            return 0;
        }

        $deleteTelegramBotCommand->execute($bot->token);

        $bot->token = config('bots.telegram.test_main_token');

        $botInfo = $getTelegramBotInfoCommand->execute($bot->token);

        $bot->username = $botInfo['result']['username'];

        $registerResult = $registerTelegramBotCommand->execute($bot->token, $bot->getWebHookUrl());

        if (!$registerResult['ok']) {
            throw new CantRegisterTelegramBotException($registerResult['description']);
        }

        $bot->save();

        $this->info('MainBot token update');
    }
}

<?php

namespace App\Console\Commands\TelegramClient;

use App\Console\Commands\TelegramClient\Exceptions\CantCheckAliveException;
use App\MainBot\RecreateMainBotCommand;
use App\Models\MainBot;
use App\Services\TelegramClient\BotFather\BotFather;
use App\Services\TelegramClient\TelegramClient;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Class MainBotCheckAlive.
 */
class MainBotCheckAlive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram_client:main_bot:check_alive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check main bot alive';

    /**
     * Execute the console command.
     *
     * @param TelegramClient $telegramClient
     *
     * @throws CantCheckAliveException
     * @throws BindingResolutionException
     *
     * @return void
     */
    public function handle(TelegramClient $telegramClient): void
    {
        if (!config('bots.test_main_token')) {
            return;
        }

        $botModel = MainBot::whereActive(true)->firstOrFail();

        $webhookAddress = route('main.bot.hook', ['slug' => $botModel->slug]);
        $webhookStatus = Http::post($webhookAddress)->status();

        if ($webhookStatus !== 200) {
            throw new CantCheckAliveException("Cant check MainBot alive status, webhook address $webhookAddress not available.");
        }

        if (!$botModel->token || !$botModel->slug || !$botModel->username) {
            throw new CantCheckAliveException('Cant check MainBot alive status. Need properties in MainBot model is empty.');
        }

        try {
            $telegramClient->getInfo(BotFather::BOT_FATHER_USERNAME);
        } catch (Throwable $th) {
            throw new CantCheckAliveException('Cant check MainBot alive status. '.$th->getMessage());
        }

        $command = '/'.config('main_bot_logic.standart.commands.alive.key');
        $expectMessage = config('main_bot_logic.standart.commands.alive.content');
        $lastMessage = '';

        try {
            $telegramClient->sendMessage($botModel->username, $command);
            sleep(5);
            $lastMessage = $telegramClient->getLastMessages($botModel->username, 1)[0]['message'];
            $telegramClient->deleteHistory($botModel->username);
        } catch (Throwable $th) {
        } finally {
            $needRecreateBot = $lastMessage !== $expectMessage;
            if ($needRecreateBot) {
                app()->make(RecreateMainBotCommand::class)->execute($botModel);
            }
        }
    }
}

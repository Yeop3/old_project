<?php

namespace App\Console\Commands\Bot;

use App\Services\MainBot\Create\CreateStandardTelegramBotCommand;
use App\Services\MainBot\Create\CreateStandardTelegramBotDto;
use Illuminate\Console\Command;

/**
 * Class CreateMainBot.
 */
class CreateMainBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main_bot:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param CreateStandardTelegramBotCommand $createStandardTelegramBotCommand
     *
     * @return int
     */
    public function handle(CreateStandardTelegramBotCommand $createStandardTelegramBotCommand): ?int
    {
        $token = config('bots.telegram.test_main_token');

        if (!$token) {
            $this->error('Fill token in env param "TELEGRAM_MAIN_BOT_TEST_TOKEN" for main bot creation');

            return 0;
        }

        $dto = new CreateStandardTelegramBotDto(
            'MainBotTest',
            $token,
            1,
        );

        $createStandardTelegramBotCommand->execute($dto);

        $this->info('MainBot created');

        return 0;
    }
}

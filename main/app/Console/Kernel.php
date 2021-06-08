<?php

namespace App\Console;

use App\Console\Commands\Bot\CreateBot;
use App\Console\Commands\BotLogic\InitBotLogic;
use App\Console\Commands\Client\UnbanedClient;
use App\Console\Commands\Init;
use App\Console\Commands\Order\CheckOrderCryptoPayment;
use App\Console\Commands\Order\CheckOrderTimeout;
use App\Console\Commands\Product\FixProductsCoordinates;
use App\Console\Commands\TelegramClient\MainBotCheckAlive;
use App\Console\Commands\User\CreateAdmin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateAdmin::class,
        Init::class,
        InitBotLogic::class,
        CreateBot::class,
        CheckOrderTimeout::class,
        UnbanedClient::class,
        CheckOrderCryptoPayment::class,
        MainBotCheckAlive::class,
        FixProductsCoordinates::class,
    ];

    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('orders:check:timeout')->everyMinute();
        $schedule->command('orders:check:crypto_payment')->everyTenMinutes();
        $schedule->command('client:unban')->everyMinute();
        $schedule->command('crypto:cache')->everyMinute();
        $schedule->command('telegram_client:main_bot:check_alive')->hourly();
        $schedule->command('global_money:access:check')->everyFiveMinutes();
        $schedule->command('proxy:check')->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

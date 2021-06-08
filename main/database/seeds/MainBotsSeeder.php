<?php

use App\Services\MainBot\Create\CreateStandardTelegramBotCommand;
use App\Services\MainBot\Create\CreateStandardTelegramBotDto;
use Illuminate\Database\Seeder;

class MainBotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function run(): void
    {
        $token = config('bots.telegram.test_main_token');

        if (!$token) {
            return;
        }

        app()->make(CreateStandardTelegramBotCommand::class)
            ->execute(
                new CreateStandardTelegramBotDto(
                    'test',
                    $token,
                    true
                )
            );
    }
}

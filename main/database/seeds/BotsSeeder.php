<?php

use App\Models\Seller;
use App\Services\Bot\Create\CreateStandardTelegramBotCommand;
use App\Services\Bot\Create\CreateStandardTelegramBotDto;
use App\Services\Bot\VO\BotLogicType;
use Illuminate\Database\Seeder;

class BotsSeeder extends Seeder
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
        $token = config('bots.telegram.test_token');

        if (!$token) {
            return;
        }

        $domain = parse_url(config('app.url'))['host'];

        $seller = Seller::whereDomain($domain)->first();

        if (!$seller) {
            return;
        }

        app()->make(CreateStandardTelegramBotCommand::class)
            ->execute(
                $seller,
                new CreateStandardTelegramBotDto(
                    'test',
                    $token,
                    1,
                    new BotLogicType(BotLogicType::STANDARD),
                    true,
                    true
                )
            );
    }
}

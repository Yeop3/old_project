<?php

namespace App\Console\Commands\Bot;

use App\Services\Bot\Create\CreateStandardTelegramBotCommand;
use App\Services\Bot\Create\CreateStandardTelegramBotDto;
use App\Services\Bot\VO\BotLogicType;
use App\Services\Seller\Create\CreateSellerCommand;
use App\Services\Seller\Create\CreateSellerDto;
use Illuminate\Console\Command;

/**
 * Class CreateBot.
 */
class CreateBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:create {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param CreateSellerCommand              $createSellerCommand
     * @param CreateStandardTelegramBotCommand $createStandardTelegramBotCommand
     */
    public function handle(CreateSellerCommand $createSellerCommand, CreateStandardTelegramBotCommand $createStandardTelegramBotCommand): void
    {
        $token = $this->argument('token');

        $seller = $createSellerCommand->execute(
            new CreateSellerDto(
                'test',
                'test.com',
                'secret',
            )
        );

        $dto = new CreateStandardTelegramBotDto(
            'test',
            $token,
            1,
            new BotLogicType(BotLogicType::STANDARD),
            true,
            true
        );

        $createStandardTelegramBotCommand->execute($seller, $dto);

        $this->info('Bot created');
    }
}

<?php

namespace Tests\Feature\Bot;

use App\Bot\Commands\GetTelegramBotInfoCommand;
use App\Bot\Register\RegisterTelegramBotCommand;
use App\Models\Seller;
use App\Services\Bot\Checker;
use App\Services\Bot\Create\CreateStandardTelegramBotCommand;
use App\Services\Bot\Create\CreateStandardTelegramBotDto;
use App\Services\Bot\Exceptions\CantRegisterTelegramBotException;
use App\Services\Bot\VO\BotLogicType;
use App\Services\BotLogic\CreateBotLogicFromConfigCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateStandardTelegramBotTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $seller = factory(Seller::class)->create();

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $dto = new CreateStandardTelegramBotDto(
            'some bot',
            'token',
            $botLogic->number,
            new BotLogicType(BotLogicType::STANDARD),
            true,
            true
        );

        $registerTelegramBotCommand = $this->mock(RegisterTelegramBotCommand::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('execute')
                ->andReturn(['ok' => true]);
        });

        $getTelegramBotInfoCommand = $this->mock(GetTelegramBotInfoCommand::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('execute')
                ->andReturn([
                    'ok'     => true,
                    'result' => ['username' => 'test'],
                ]);
        });

        $registerTelegramBotCommand = new CreateStandardTelegramBotCommand($registerTelegramBotCommand, new Checker(), $getTelegramBotInfoCommand);

        $bot = $registerTelegramBotCommand->execute($seller, $dto);

        $this->assertDatabaseHas('bots', ['slug' => $bot->slug]);
    }

    public function test_create_fail(): void
    {
        $seller = factory(Seller::class)->create();

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $dto = new CreateStandardTelegramBotDto(
            'some bot',
            'token',
            $botLogic->number,
            new BotLogicType(BotLogicType::STANDARD),
            true,
            true
        );

        $registerTelegramBotCommand = $this->mock(RegisterTelegramBotCommand::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('execute')
                ->andReturn(['ok' => false, 'description' => 'some error']);
        });

        $getTelegramBotInfoCommand = $this->mock(GetTelegramBotInfoCommand::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('execute')
                ->andReturn([
                    'ok'     => true,
                    'result' => ['username' => 'test'],
                ]);
        });

        $registerTelegramBotCommand = new CreateStandardTelegramBotCommand($registerTelegramBotCommand, new Checker(), $getTelegramBotInfoCommand);

        $this->expectException(CantRegisterTelegramBotException::class);

        $registerTelegramBotCommand->execute($seller, $dto);
    }
}

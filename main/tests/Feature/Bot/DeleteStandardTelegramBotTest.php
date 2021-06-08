<?php

namespace Tests\Feature\Bot;

use App\Bot\Register\DeleteTelegramBotCommand;
use App\Models\Bot;
use App\Services\Bot\Delete\DeleteStandardTelegramBotCommand;
use App\Services\BotLogic\CreateBotLogicFromConfigCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteStandardTelegramBotTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $seller = $sellerUser->seller;

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $bot = factory(Bot::class)
            ->states(['telegram', 'standard'])
            ->create(['seller_id' => $seller->id, 'logic_id' => $botLogic->id]);

        $deleteTelegramBotCommand = $this->mock(DeleteTelegramBotCommand::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('execute')
                ->andReturn(['ok' => true]);
        });

        $deleteTelegramBotCommand = new DeleteStandardTelegramBotCommand($deleteTelegramBotCommand);

        $deleteTelegramBotCommand->execute($bot->number, $seller);

        $this->getJson('/api/bots/'.$bot->number)
            ->assertStatus(404);
    }
}

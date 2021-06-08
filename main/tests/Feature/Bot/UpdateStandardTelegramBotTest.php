<?php

namespace Tests\Feature\Bot;

use App\Models\Bot;
use App\Services\Bot\VO\BotLogicType;
use App\Services\BotLogic\CreateBotLogicFromConfigCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateStandardTelegramBotTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $bot = factory(Bot::class)
            ->states(['telegram', 'standard'])
            ->create(['seller_id' => $sellerUser->seller_id, 'logic_id' => $botLogic->id]);

        $this->putJson('/api/bots/'.$bot->number, [
            'name'                 => 'Ivan',
            'logic_number'         => $botLogic->number,
            'logic_type'           => BotLogicType::STANDARD,
            'active'               => true,
            'allow_create_clients' => true,
        ])
            ->assertStatus(200);

        $this->getJson('/api/bots/'.$bot->number)
            ->assertOk()
            ->assertJsonPath('name', 'Ivan');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $bot = factory(Bot::class)
            ->states(['telegram', 'standard'])
            ->create(['seller_id' => $sellerUser->seller_id, 'logic_id' => $botLogic->id]);

        $this
            ->putJson('/api/bots/'.$bot->number, [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }

    public function test_update_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $botLogic = (new CreateBotLogicFromConfigCommand())->execute('standard');

        $bot1 = factory(Bot::class)
            ->states(['telegram', 'standard'])
            ->create(['seller_id' => $sellerUser->seller_id, 'logic_id' => $botLogic->id]);
        $bot2 = factory(Bot::class)
            ->states(['telegram', 'standard'])
            ->create(['seller_id' => $sellerUser->seller_id, 'logic_id' => $botLogic->id]);

        $this
            ->putJson('/api/bots/'.$bot1->number, [
                'name'                 => $bot2->name,
                'logic_number'         => $botLogic->number,
                'logic_type'           => BotLogicType::STANDARD,
                'active'               => true,
                'allow_create_clients' => true,
            ])
            ->assertStatus(422);
    }
}

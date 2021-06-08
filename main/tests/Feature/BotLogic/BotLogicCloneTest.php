<?php

namespace Tests\Feature\BotLogic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class BotLogicCloneTest extends TestCase
{
    use RefreshDatabase;

    public function test_clone_success(): void
    {
        $this->artisan('bot_logics:init');
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $number = $this->postJson('/api/bot_logics/standard/1/clone')
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/bot_logics/client/'.$number)
            ->assertStatus(200)
            ->assertJsonStructure(BotLogicHelper::LOGIC_JSON_STRUCTURE);
    }
}

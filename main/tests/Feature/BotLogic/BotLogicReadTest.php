<?php

namespace Tests\Feature\BotLogic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class BotLogicReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_success(): void
    {
        $this->artisan('bot_logics:init');
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this->getJson('/api/bot_logics')
            ->assertOk()
            ->json();
    }

    public function test_show_success(): void
    {
        $this->artisan('bot_logics:init');
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this->getJson('/api/bot_logics/standard/1')
            ->assertOk()
            ->assertJsonStructure(BotLogicHelper::LOGIC_JSON_STRUCTURE);
    }
}

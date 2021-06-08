<?php

namespace Tests\Feature\BotLogic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class BotLogicDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $this->artisan('bot_logics:init');
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $number = $this->postJson('/api/bot_logics/standard/1/clone')
            ->assertStatus(201)
            ->json('number');

        $this->deleteJson('/api/bot_logics/client/'.$number)
            ->assertStatus(200);

        $this->getJson('/api/bot_logics/client/'.$number)
            ->assertStatus(404);
    }
}

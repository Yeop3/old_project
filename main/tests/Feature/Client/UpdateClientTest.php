<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $client = factory(Client::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->putJson('/api/clients/'.$client->number, [
            'note'              => 'some note',
            'discount_value'    => 10,
            'discount_priority' => 100,
        ])
            ->assertStatus(200);

        $this->getJson('/api/clients/'.$client->number)
            ->assertOk()
            ->assertJsonPath('note', 'some note')
            ->assertJsonPath('discount_value', 10)
            ->assertJsonPath('discount_priority', '100');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $client = factory(Client::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/clients/'.$client->number, [
                'note' => 'Ivan',
            ])
            ->assertStatus(401);
    }
}

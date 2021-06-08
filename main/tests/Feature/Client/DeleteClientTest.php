<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
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

        $this->deleteJson('/api/clients/'.$client->number)
            ->assertOk();

        $this->getJson('/api/clients/'.$client->number)
            ->assertStatus(404);
    }

    public function test_delete_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $client = factory(Client::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->deleteJson('/api/clients/'.$client->number)
            ->assertStatus(401);
    }
}

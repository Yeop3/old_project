<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->putJson('/api/drivers/'.$driver->number, [
            'name' => 'Ivan',
        ])
            ->assertStatus(200);

        $this->getJson('/api/drivers/'.$driver->number)
            ->assertOk()
            ->assertJsonPath('name', 'Ivan');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/drivers/'.$driver->number, [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }

    public function test_update_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);
        $driver1 = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/drivers/'.$driver->number, [
                'name' => $driver1->name,
            ])
            ->assertStatus(422);
    }
}

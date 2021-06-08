<?php

namespace Tests\Feature\Location;

use App\Models\Location;
use App\VO\CommissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->putJson('/api/locations/'.$location->number, [
            'name'             => 'Blabla',
            'is_branch'        => false,
            'commission_value' => 20,
            'commission_type'  => CommissionType::TYPE_PERCENT,
            'priority'         => 1,
        ])
            ->assertStatus(200);

        $this->getJson('/api/locations/'.$location->number)
            ->assertOk()
            ->assertJsonPath('name', 'Blabla');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/locations/'.$location->number, [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }

    public function test_update_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);
        $location1 = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/locations/'.$location->number, [
                'name' => $location1->name,
            ])
            ->assertStatus(422);
    }
}

<?php

namespace Tests\Feature\Location;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('api/locations/'.$location->number)
            ->assertOk();

        $this->getJson('api/locations'.$location->number)
            ->assertStatus(404);
    }
}

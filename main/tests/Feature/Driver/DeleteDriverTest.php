<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('api/drivers/'.$driver->number)
            ->assertOk();

        $this->getJson('api/drivers'.$driver->number)
            ->assertStatus(404);
    }
}

<?php

namespace Tests\Feature\Product;

use App\Models\Driver;
use App\Models\Location;
use App\Models\ProductType;
use App\Services\Product\VO\ProductStatus;
use App\VO\CommissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);
        $productType = factory(ProductType::class)->create(['seller_id' => $sellerUser->seller_id]);
        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $id = $this
            ->postJson('/api/products', [
                'driver_id'        => $driver->id,
                'product_type_id'  => $productType->id,
                'location_id'      => $location->id,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'address'          => 'some address',
                'status'           => ProductStatus::STATUS_SELL,
            ])
            ->assertStatus(201)
            ->json('number');

        $this->deleteJson('api/products/'.$id)
            ->assertOk();

        $this->getJson('api/products/'.$id)
            ->assertStatus(404);
    }
}

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

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);
        $productType = factory(ProductType::class)->create(['seller_id' => $sellerUser->seller_id]);
        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $id = $this
            ->postJson('/api/products', [
                'driver_id'        => $driver->number,
                'product_type_id'  => $productType->number,
                'location_id'      => $location->number,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'address'          => 'some address',
                'status'           => ProductStatus::STATUS_SELL,
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/products/'.$id)
            ->assertOk()
            ->assertJsonPath('address', 'some address');
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/products')
            ->assertStatus(401);
    }

    public function test_create_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);
        $productType = factory(ProductType::class)->create(['seller_id' => $sellerUser->seller_id]);
        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->postJson('/api/products', [
                'driver_id'        => $driver->number,
                'product_type_id'  => $productType->number,
                'location_id'      => $location->number,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'address'          => 'some address',
                'status'           => ProductStatus::STATUS_SELL,
            ])
            ->assertStatus(201);

        $tes = $this
            ->postJson('/api/products', [
                'driver_id'        => $driver->number,
                'product_type_id'  => $productType->number,
                'location_id'      => $location->number,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'address'          => 'some address',
                'status'           => ProductStatus::STATUS_SELL,
                'check_duplicates' => true,
            ])
            ->assertStatus(422);
    }
}

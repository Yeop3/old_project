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

class MassCreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $driver = factory(Driver::class)->create(['seller_id' => $sellerUser->seller_id]);
        $productType = factory(ProductType::class)->create(['seller_id' => $sellerUser->seller_id]);
        $location = factory(Location::class)->create(['seller_id' => $sellerUser->seller_id]);

        $response = $this
            ->postJson('/api/products/create_mass', [
                'driver_id'        => $driver->number,
                'product_type_id'  => $productType->number,
                'location_id'      => $location->number,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'addresses'        => [
                    'some address1',
                    'some address2',
                    'some address3',
                ],
                'status' => ProductStatus::STATUS_SELL,
            ])
            ->assertStatus(200)
            ->assertJsonPath('total_count', 3)
            ->assertJsonPath('failed_count', 0)
            ->assertJsonCount(3, 'products')
            ->json();

        $this->getJson('/api/products/'.$response['products'][0]['number'])
            ->assertOk()
            ->assertJsonPath('address', 'some address1');

        $this->getJson('/api/products/'.$response['products'][1]['number'])
            ->assertOk()
            ->assertJsonPath('address', 'some address2');

        $this->getJson('/api/products/'.$response['products'][2]['number'])
            ->assertOk()
            ->assertJsonPath('address', 'some address3');
    }

    public function test_create_with_duplicate_fail(): void
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
                'address'          => 'some address1',
                'status'           => ProductStatus::STATUS_SELL,
            ])
            ->assertStatus(201);

        $response = $this
            ->postJson('/api/products/create_mass', [
                'driver_id'        => $driver->id,
                'product_type_id'  => $productType->id,
                'location_id'      => $location->id,
                'commission_value' => 10,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'addresses'        => [
                    'some address1',
                    'some address2',
                    'some address3',
                ],
                'status'           => ProductStatus::STATUS_SELL,
                'check_duplicates' => true,
            ])
            ->assertStatus(200)
            ->assertJsonPath('total_count', 3)
            ->assertJsonPath('failed_count', 1)
            ->assertJsonCount(2, 'products')
            ->json();

        $this->getJson('/api/products/'.$response['products'][0]['number'])
            ->assertOk()
            ->assertJsonPath('address', 'some address2');

        $this->getJson('/api/products/'.$response['products'][1]['number'])
            ->assertOk()
            ->assertJsonPath('address', 'some address3');
    }
}

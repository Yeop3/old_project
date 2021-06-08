<?php

namespace Tests\Feature\Discount;

use App\Models\Discount;
use App\Models\Location;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $productTypes = factory(ProductType::class, 5)->create(['seller_id' => $sellerUser->seller_id]);
        $locations = factory(Location::class, 5)->create(['seller_id' => $sellerUser->seller_id]);
        $discount = factory(Discount::class)
            ->create(['seller_id' => $sellerUser->seller_id]);

        $discount->locations()->sync($locations->pluck('id'));
        $discount->productTypes()->sync($productTypes->pluck('id'));

        $otherProductTypes = factory(ProductType::class, 3)->create(['seller_id' => $sellerUser->seller_id]);
        $otherLocations = factory(Location::class, 3)->create(['seller_id' => $sellerUser->seller_id]);

        $dateStart = now()->addDay()->toDateTimeString();
        $dateEnd = now()->addDays(14)->toDateTimeString();

        $this->putJson('/api/discounts/'.$discount->number, [
            'name'                         => 'Test',
            'description'                  => 'desc',
            'discount_value'               => 10,
            'discount_priority'            => 100,
            'active'                       => true,
            'date_start'                   => $dateStart,
            'date_end'                     => $dateEnd,
            'client_min_paid_orders_count' => 0,
            'client_min_income'            => 0,
            'product_type_numbers'         => $otherProductTypes->pluck('number')->toArray(),
            'location_numbers'             => $otherLocations->pluck('number')->toArray(),
        ])
            ->assertStatus(200);

        $this->getJson('/api/discounts/'.$discount->number)
            ->assertOk()
            ->assertJsonPath('name', 'Test')
            ->assertJsonCount(3, 'product_types')
            ->assertJsonCount(3, 'locations');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $discount = factory(Discount::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/discounts/'.$discount->number, [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }
}

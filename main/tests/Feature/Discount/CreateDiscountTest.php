<?php

namespace Tests\Feature\Discount;

use App\Models\Location;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class CreateDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $productTypes = factory(ProductType::class, 5)->create(['seller_id' => $sellerUser->seller_id]);
        $locations = factory(Location::class, 5)->create(['seller_id' => $sellerUser->seller_id]);

        $dateStart = now()->addDay()->toDateTimeString();
        $dateEnd = now()->addDays(14)->toDateTimeString();

        $number = $this
            ->postJson('/api/discounts', [
                'name'                         => 'Test',
                'description'                  => 'desc',
                'discount_value'               => 10,
                'discount_priority'            => 100,
                'active'                       => true,
                'date_start'                   => $dateStart,
                'date_end'                     => $dateEnd,
                'client_min_paid_orders_count' => 0,
                'client_min_income'            => 0,
                'product_type_numbers'         => $productTypes->pluck('number')->toArray(),
                'location_numbers'             => $locations->pluck('number')->toArray(),
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/discounts/'.$number)
            ->assertOk()
            ->assertJsonPath('name', 'Test')
            ->assertJsonPath('description', 'desc')
            ->assertJsonPath('discount_value', 10)
            ->assertJsonPath('discount_priority', '100')
            ->assertJsonPath('active', true)
            ->assertJsonPath('date_start', $dateStart)
            ->assertJsonPath('date_end', $dateEnd)
            ->assertJsonPath('client_min_paid_orders_count', '0')
            ->assertJsonPath('client_min_income', '0')
            ->assertJsonCount(5, 'product_types')
            ->assertJsonCount(5, 'locations');
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/discounts', [
                'name' => 'Test',
            ])
            ->assertStatus(401);
    }
}

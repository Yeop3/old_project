<?php

namespace Tests\Feature\Discount;

use App\Models\Discount;
use App\Models\Location;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class ReadDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $productTypes = factory(ProductType::class, 5)->create(['seller_id' => $sellerUser->seller_id]);
        $locations = factory(Location::class, 5)->create(['seller_id' => $sellerUser->seller_id]);
        $discount = factory(Discount::class)
            ->create(['seller_id' => $sellerUser->seller_id]);

        $discount->locations()->sync($locations->pluck('id'));
        $discount->productTypes()->sync($productTypes->pluck('id'));

        $this->getJson('api/discounts')
            ->assertJsonStructure([
                'data' => [
                    [
                        'number',
                        'name',
                        'discount_value',
                        'discount_priority',
                        'date_start',
                        'date_end',
                        'created_at',
                        'product_types' => [
                            [
                                'number',
                                'name',
                                'products',
                            ],
                        ],
                        'locations' => [
                            [
                                'number',
                                'name',
                                'name_chain',
                            ],
                        ],
                        'client_min_paid_orders_count',
                        'client_min_income',
                        'active',
                    ],
                ],
            ]);
    }
}

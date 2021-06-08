<?php

namespace Tests\Feature\Discount;

use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $discount = factory(Discount::class)
            ->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('api/discounts/'.$discount->number)
            ->assertOk();

        $this->getJson('api/discounts'.$discount->number)
            ->assertStatus(404);
    }
}

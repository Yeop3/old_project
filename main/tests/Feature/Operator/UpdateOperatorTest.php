<?php

namespace Tests\Feature\Operator;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateOperatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $operator = factory(Operator::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->putJson('/api/operators/'.$operator->number, [
            'name' => 'Ivan',
        ])
            ->assertStatus(200);

        $this->getJson('/api/operators/'.$operator->number)
            ->assertOk()
            ->assertJsonPath('name', 'Ivan');
    }

    public function test_create_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);
        $operator = factory(Operator::class, 2)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/operators/'.$operator[0]->number, [
                'name' => $operator[1]->name,
            ])->assertStatus(422);
    }
}

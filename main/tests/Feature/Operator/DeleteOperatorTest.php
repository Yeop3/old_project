<?php

namespace Tests\Feature\Operator;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteOperatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $operator = factory(Operator::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('api/operators/'.$operator->number)
            ->assertOk();

        $this->getJson('api/operators/'.$operator->number)
            ->assertStatus(404);
    }
}

<?php

namespace Tests\Feature\Shift;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\SellerHelper;
use Tests\TestCase;

class StartNewShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_new_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $operators = $this->getJson('/api/operators')
            ->json('data');

        $randomOperator = Arr::random($operators);

        $this->postJson('/api/shifts/start_new/'.$randomOperator['number'])
            ->assertStatus(201);

        $this->getJson('/api/shifts/current')
            ->assertOk()
            ->assertJsonPath('operator.number', $randomOperator['number']);
    }
}

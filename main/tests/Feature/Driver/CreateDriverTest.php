<?php

namespace Tests\Feature\Driver;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class CreateDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $number = $this
            ->postJson('/api/drivers', [
                'name' => 'Ivan',
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/drivers/'.$number)
            ->assertOk()
            ->assertJsonPath('name', 'Ivan');
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/drivers', [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }

    public function test_create_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this
            ->postJson('/api/drivers', [
                'name' => 'Ivan',
            ])
            ->assertStatus(201)
            ->json('id');

        $this
            ->postJson('/api/drivers', [
                'name' => 'Ivan',
            ])
            ->assertStatus(422);
    }
}

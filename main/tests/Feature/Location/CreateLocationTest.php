<?php

namespace Tests\Feature\Location;

use App\VO\CommissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class CreateLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_tree_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $id = $this
            ->postJson('/api/locations', [
                'name'             => 'Moscow',
                'is_branch'        => false,
                'commission_value' => 20,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'priority'         => 1,
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/locations/'.$id)
            ->assertOk()
            ->assertJsonPath('name', 'Moscow');

        $id1 = $this
            ->postJson('/api/locations', [
                'name'             => 'Moscow1',
                'is_branch'        => false,
                'commission_value' => 20,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'priority'         => 1,
                'parent_number'    => $id,
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/locations/'.$id1)
            ->assertOk()
            ->assertJsonPath('name', 'Moscow1')
            ->assertJsonPath('parent.name', 'Moscow')
            ->json();

        $id2 = $this
            ->postJson('/api/locations', [
                'name'             => 'Moscow2',
                'is_branch'        => false,
                'commission_value' => 20,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'priority'         => 1,
                'parent_number'    => $id1,
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/locations/'.$id2)
            ->assertOk()
            ->assertJsonPath('name', 'Moscow2')
            ->assertJsonPath('parent.name', 'Moscow1');

        $this->getJson('/api/locations')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Moscow')
            ->assertJsonPath('data.0.children.0.name', 'Moscow1')
            ->assertJsonPath('data.0.children.0.children.0.name', 'Moscow2');
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/locations', [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }

    public function test_create_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this
            ->postJson('/api/locations', [
                'name'             => 'Moscow',
                'is_branch'        => false,
                'commission_value' => 20,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'priority'         => 1,
            ])
            ->assertStatus(201)
            ->json('id');

        $this
            ->postJson('/api/locations', [
                'name'             => 'Moscow',
                'is_branch'        => false,
                'commission_value' => 20,
                'commission_type'  => CommissionType::TYPE_PERCENT,
                'priority'         => 1,
            ])
            ->assertStatus(422);
    }
}

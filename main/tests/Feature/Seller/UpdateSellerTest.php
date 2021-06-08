<?php

namespace Tests\Feature\Seller;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSellerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $admin = factory(User::class)->states('admin')->create();
        $this->actingAs($admin);

        $seller = factory(Seller::class)->create();

        $this->putJson('/api/sellers/'.$seller->id, [
            'name'   => 'some name 123',
            'domain' => 'somesite123.com',
        ])
            ->assertStatus(200);

        $this->getJson('/api/sellers/'.$seller->id)
            ->assertOk()
            ->assertJsonPath('name', 'some name 123')
            ->assertJsonPath('domain', 'somesite123.com');
    }

    public function test_update_by_not_admin_fail(): void
    {
        $sellerUser = factory(User::class)->states('seller')->create();
        $this->actingAs($sellerUser);

        $seller = factory(Seller::class)->create();

        $this
            ->putJson('/api/sellers/'.$seller->id, [
                'name'   => 'some name',
                'domain' => 'somesite.com',
            ])
            ->assertStatus(403);
    }

    public function test_update_by_not_auth_fail(): void
    {
        $seller = factory(Seller::class)->create();

        $this
            ->putJson('/api/sellers/'.$seller->id, [
                'name'   => 'some name',
                'domain' => 'somesite.com',
            ])
            ->assertStatus(401);
    }

    public function test_update_domain_duplicate_fail(): void
    {
        $admin = factory(User::class)->states('admin')->create();
        $this->actingAs($admin);

        $seller = factory(Seller::class)->create();
        $seller1 = factory(Seller::class)->create();

        $this
            ->putJson('/api/sellers/'.$seller1->id, [
                'name'   => 'some name',
                'domain' => $seller->domain,
            ])
            ->assertStatus(422);
    }
}

<?php

namespace Tests\Feature\Seller;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSellerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $admin = factory(User::class)->states('admin')->create();
        $this->actingAs($admin);

        $id = $this
            ->postJson('/api/sellers', [
                'name'                  => 'some name',
                'domain'                => 'somesite.com',
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertStatus(201)
            ->json('id');

        $this->getJson('/api/sellers/'.$id)
            ->assertOk()
            ->assertJsonPath('name', 'some name')
            ->assertJsonPath('domain', 'somesite.com');
    }

    public function test_create_by_not_admin_fail(): void
    {
        $seller = factory(User::class)->states('seller')->create();
        $this->actingAs($seller);

        $this
            ->postJson('/api/sellers', [
                'name'                  => 'some name',
                'domain'                => 'somesite.com',
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertStatus(403);
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/sellers', [
                'name'                  => 'some name',
                'domain'                => 'somesite.com',
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertStatus(401);
    }

    public function test_create_domain_duplicate_fail(): void
    {
        $admin = factory(User::class)->states('admin')->create();
        $this->actingAs($admin);

        $this
            ->postJson('/api/sellers', [
                'name'                  => 'some name',
                'domain'                => 'somesite.com',
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertStatus(201)
            ->json('id');

        $this
            ->postJson('/api/sellers', [
                'name'                  => 'some name',
                'domain'                => 'somesite.com',
                'password'              => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertStatus(422);
    }
}

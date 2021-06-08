<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class SellerLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();

        $this
            ->postJson('/api/login', [
                'password' => 'password',
            ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_login_on_wrong_domain_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser(false);

        $this
            ->postJson('/api/login', [
                'password' => 'password',
            ])
            ->assertStatus(401);
    }
}

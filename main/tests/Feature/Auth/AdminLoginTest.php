<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success(): void
    {
        $admin = factory(User::class)->states('admin')->create();

        $this
            ->postJson('/api/admin/login', [
                'email'    => $admin->email,
                'password' => 'password',
            ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }
}

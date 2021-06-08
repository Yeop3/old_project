<?php

namespace Tests\Feature\SellerSetting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class SellerSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $settings = $this
            ->getJson('/api/settings')
            ->assertStatus(200)
            ->json();

        $this
            ->putJson('/api/settings', ['sections' => $settings])
            ->assertStatus(200);
    }
}

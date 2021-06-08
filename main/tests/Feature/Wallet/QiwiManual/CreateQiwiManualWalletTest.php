<?php

namespace Tests\Feature\Wallet\QiwiManual;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class CreateQiwiManualWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $number = $this
            ->postJson('/api/wallets/qiwi_manual', [
                'phone'                 => '+380507777777',
                'active'                => true,
                'min_paid_orders_count' => 0,
                'note'                  => 'some note',
            ])
            ->assertStatus(201)
            ->json('number');

        $this->getJson('/api/wallets/qiwi_manual/'.$number)
            ->assertOk()
            ->assertJsonPath('phone', '+380507777777');
    }

    public function test_create_by_not_auth_fail(): void
    {
        $this
            ->postJson('/api/wallets/qiwi_manual')
            ->assertStatus(401);
    }

    public function test_create_duplicate_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this
            ->postJson('/api/wallets/qiwi_manual', [
                'phone'                 => '+380507777777',
                'active'                => true,
                'min_paid_orders_count' => 0,
                'note'                  => 'some note',
            ])
            ->assertStatus(201);

        $this
            ->postJson('/api/wallets/qiwi_manual', [
                'phone'                 => '+380507777777',
                'active'                => true,
                'min_paid_orders_count' => 0,
                'note'                  => 'some note',
            ])
            ->assertStatus(422);
    }
}

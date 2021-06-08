<?php

namespace Tests\Feature\Wallet\QiwiManual;

use App\Models\Wallet\QiwiManualWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SellerHelper;
use Tests\TestCase;

class UpdateQiwiManualWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->putJson('/api/wallets/qiwi_manual/'.$wallet->number, [
            'active'                => false,
            'min_paid_orders_count' => 1,
            'note'                  => 'some note',
        ])
            ->assertStatus(200);

        $this->getJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertOk()
            ->assertJsonPath('active', false)
            ->assertJsonPath('min_paid_orders_count', '1');
    }

    public function test_update_by_not_auth_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this
            ->putJson('/api/wallets/qiwi_manual/'.$wallet->number, [
                'name' => 'Ivan',
            ])
            ->assertStatus(401);
    }
}

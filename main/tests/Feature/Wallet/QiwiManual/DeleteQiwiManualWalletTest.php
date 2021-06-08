<?php

namespace Tests\Feature\Wallet\QiwiManual;

use App\Models\Wallet\QiwiManualWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\OrderHelper;
use Tests\SellerHelper;
use Tests\TestCase;

class DeleteQiwiManualWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertOk();

        $this->getJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertStatus(404);

        $this->getJson('/api/wallets/qiwi_manual/deleted')
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/wallets/qiwi_manual/deleted/'.$wallet->number)
            ->assertOk();
    }

    public function test_delete_forever_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('/api/wallets/qiwi_manual/'.$wallet->number.'/forever')
            ->assertOk();

        $this->getJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertStatus(404);

        $this->getJson('/api/wallets/qiwi_manual/deleted/'.$wallet->number)
            ->assertStatus(404);
    }

    public function test_clear_soft_deleted_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertOk();

        $this->postJson('/api/wallets/qiwi_manual/deleted/clear')
            ->assertOk();

        $this->getJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertStatus(404);

        $this->getJson('/api/wallets/qiwi_manual/deleted/'.$wallet->number)
            ->assertStatus(404);
    }

    public function test_restore_deleted_success(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $wallet = factory(QiwiManualWallet::class)->create(['seller_id' => $sellerUser->seller_id]);

        $this->deleteJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertOk();

        $this->postJson('/api/wallets/qiwi_manual/deleted/'.$wallet->number.'/restore')
            ->assertOk();

        $this->getJson('/api/wallets/qiwi_manual/'.$wallet->number)
            ->assertOk();
    }

    public function test_delete_with_orders_fail(): void
    {
        $sellerUser = SellerHelper::getSellerUser();
        $this->actingAs($sellerUser);

        $this->artisan('bot_logics:init');
        $this->seed();

        $order = OrderHelper::createOrder($sellerUser->seller);

        $this->deleteJson('/api/wallets/qiwi_manual/'.$order->wallet->number)
            ->assertStatus(422);
    }
}

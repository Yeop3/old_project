<?php

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;
use Illuminate\Database\Seeder;

class WalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            if (QiwiManualWallet::whereSellerId($seller->id)->exists()) {
                continue;
            }

            factory(QiwiManualWallet::class, 1)->create(['seller_id' => $seller->id]);
        }
    }
}

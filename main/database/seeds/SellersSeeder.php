<?php

use Illuminate\Database\Seeder;
use Tests\SellerHelper;

class SellersSeeder extends Seeder
{
    public function run(): void
    {
        if (\App\Models\Seller::count()) {
            return;
        }

        SellerHelper::getSellerUser();

        SellerHelper::getSellerUser(false, 'nullzeroclient.tk');

        for ($i = 0; $i < 4; $i++) {
            SellerHelper::getSellerUser(false);
        }
    }
}

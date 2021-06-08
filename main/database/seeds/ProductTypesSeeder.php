<?php

use App\Models\ProductType;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class ProductTypesSeeder extends Seeder
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
            if (ProductType::whereSellerId($seller->id)->exists()) {
                continue;
            }

            factory(ProductType::class, 10)
                ->create([
                    'seller_id'       => $seller->id,
                    'payment_methods' => \App\Services\Wallet\VO\PaymentMethod::getInitTypesForProductTypes(),
                ]);
        }
    }
}

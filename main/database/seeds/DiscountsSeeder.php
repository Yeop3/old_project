<?php

use App\Models\Discount;
use App\Models\Location;
use App\Models\ProductType;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class DiscountsSeeder extends Seeder
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
            $productTypes = ProductType::whereSellerId($seller->id)->get();
            $locations = Location::whereSellerId($seller->id)->whereDoesntHave('children')->get();

            for ($i = 0; $i < 3; $i++) {
                $discount = factory(Discount::class)->create(['seller_id' => $seller->id]);

                $discount->productTypes()->sync($productTypes->random(mt_rand(1, min($productTypes->count(), 3))));
                $discount->locations()->sync($locations->random(mt_rand(1, min($locations->count(), 3))));
            }
        }
    }
}

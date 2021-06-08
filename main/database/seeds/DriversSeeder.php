<?php

use App\Models\Driver;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class DriversSeeder extends Seeder
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
            if (Driver::whereSellerId($seller->id)->exists()) {
                continue;
            }

            factory(Driver::class, 10)->create(['seller_id' => $seller->id]);
        }
    }
}

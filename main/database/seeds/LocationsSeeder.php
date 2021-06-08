<?php

use App\Models\Location;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
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
            if (Location::whereSellerId($seller->id)->exists()) {
                continue;
            }

            for ($k = 0; $k < 5; $k++) {
                $location = factory(Location::class)->create([
                    'seller_id' => $seller->id,
                    'parent_id' => null,
                ]);

                for ($i = 0; $i < 3; $i++) {
                    $subLocation = factory(Location::class)->create([
                        'seller_id' => $seller->id,
                        'parent_id' => $location->id,
                    ]);

//                    for ($n = 0; $n < 3; $n++) {
//                        factory(Location::class)->create([
//                            'seller_id' => $seller->id,
//                            'parent_id' => $subLocation->id,
//                        ]);
//                    }
                }
            }
        }
    }
}

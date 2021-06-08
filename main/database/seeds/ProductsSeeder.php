<?php

use App\Models\Driver;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\Product\Create\CreateProductCommand;
use App\Services\Product\ProductDto;
use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
//        $sellers = Seller::all();
//
//        $faker = \Faker\Factory::create('Ru_RU');
//
//        $command = app()->make(CreateProductCommand::class);
//
//        foreach ($sellers as $seller) {
//            if (Product::whereSellerId($seller->id)->exists()) {
//                continue;
//            }
//
//            $drivers = Driver::whereSellerId($seller->id)->get();
//            $locations = Location::whereSellerId($seller->id)->whereDoesntHave('children')->get();
//            $productTypes = ProductType::whereSellerId($seller->id)->get();
//
//            for ($i = 0; $i < 20; $i++) {
//                $command->execute(
//                    $seller,
//                    new ProductDto(
//                        $drivers->random()->number,
//                        $productTypes->random()->number,
//                        $locations->random()->number,
//                        new Commission(mt_rand(0, 100), new CommissionType(\Illuminate\Support\Arr::random(array_keys(CommissionType::TYPES)))),
//                        new ProductStatus(\Illuminate\Support\Arr::random([ProductStatus::STATUS_SELL, ProductStatus::STATUS_NOT_ACTIVE])),
//
//                        $faker->address,
//                    )
//                );
//            }
//        }
    }
}

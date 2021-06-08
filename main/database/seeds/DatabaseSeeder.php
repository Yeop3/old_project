<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        //factory(App\Models\User::class, 1)->state('admin')->create();
        //factory(App\Models\Seller::class, 1)->create();

        $this->call(SellersSeeder::class);
        $this->call(DriversSeeder::class);
        $this->call(LocationsSeeder::class);
        $this->call(ProductTypesSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(DiscountsSeeder::class);
        $this->call(BotsSeeder::class);
        $this->call(WalletsSeeder::class);
    }
}

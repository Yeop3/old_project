<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Discount::class, function (Faker $faker) {
    return [
        'name'                         => $faker->name,
        'description'                  => $faker->text(),
        'discount_value'               => new \App\Services\Discount\VO\DiscountValue(mt_rand(0, 99)),
        'discount_priority'            => mt_rand(0, 200),
        'active'                       => true,
        'date_range'                   => new \App\Services\Discount\VO\DiscountDateRange(now()->subDay(), now()->addDays(mt_rand(5, 20))),
        'client_min_paid_orders_count' => 0,
        'client_min_income'            => 0,
    ];
});

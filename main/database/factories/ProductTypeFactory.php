<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\VO\CommissionType;
use App\VO\Unit;
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

$factory->define(\App\Models\ProductType::class, function (Faker $faker) {
    return [
        'name'       => $faker->words(mt_rand(1, 2), true),
        'price'      => new \Money\Money(mt_rand(1000, 10000), new \Money\Currency('RUR')),
        'commission' => new \App\VO\Commission(
            mt_rand(0, 100),
            new CommissionType(Arr::random(array_keys(CommissionType::TYPES))),
        ),
        'packing'      => mt_rand(1, 100),
        'real_packing' => mt_rand(1, 100),
        'unit'         => new Unit(Arr::random(array_keys(Unit::UNITS))),
        'priority'     => mt_rand(1, 20),
    ];
});

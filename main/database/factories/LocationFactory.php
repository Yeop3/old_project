<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\VO\CommissionType;
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

$factory->define(\App\Models\Location::class, function (Faker $faker) {
    return [
        'name'       => $faker->city,
        'commission' => new \App\VO\Commission(
            mt_rand(0, 100),
            new CommissionType(Arr::random(array_keys(CommissionType::TYPES))),
        ),
        'is_branch' => $faker->boolean,
        'priority'  => mt_rand(1, 20),
    ];
});

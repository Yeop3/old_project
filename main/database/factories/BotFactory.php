<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Services\Bot\VO\BotType;
use App\Services\Bot\VO\Messenger;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(\App\Models\Bot::class, function (Faker $faker) {
    return [
        'name'                 => $faker->name,
        'username'             => $faker->name,
        'token'                => $faker->name,
        'slug'                 => Str::random(),
        'active'               => true,
        'allow_create_clients' => true,
    ];
});

$factory->state(\App\Models\Bot::class, 'telegram', function (Faker $faker) {
    return [
        'messenger' => new Messenger(Messenger::TELEGRAM),
    ];
});

$factory->state(\App\Models\Bot::class, 'standard', function (Faker $faker) {
    return [
        'type' => new BotType(BotType::STANDARD),
    ];
});

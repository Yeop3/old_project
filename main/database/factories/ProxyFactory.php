<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Proxy::class, function (Faker $faker) {
    return [
        'seller_id'  => 1,
        'ip'         => inet_pton($faker->ipv4),
        'port'       => random_int(1000, 9999),
        'proxy_type' => 'socks5',
    ];
});

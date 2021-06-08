<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Operator;
use App\Models\Seller;
use App\Models\Shift;
use App\Models\User;

/**
 * Class SellerHelper.
 */
final class SellerHelper
{
    public static function getSellerUser(bool $defaultDomain = true, ?string $domain = null): User
    {
        if ($defaultDomain) {
            $urlParams = collect(parse_url(config('app.url')));

            $domain = $urlParams->get('host');
            $seller = factory(Seller::class)->create(['domain' => $domain]);
        } elseif ($domain) {
            $seller = factory(Seller::class)->create(['domain' => $domain]);
        } else {
            $seller = factory(Seller::class)->create();
        }

        $firstOperator = factory(Operator::class)->create([
            'seller_id' => $seller->id,
            'name'      => 'День',
        ]);

        factory(Operator::class)->create([
            'seller_id' => $seller->id,
            'name'      => 'Ночь',
        ]);

        factory(Shift::class)->create([
            'seller_id'   => $seller->id,
            'operator_id' => $firstOperator->id,
            'started_at'  => now(),
        ]);

        return factory(User::class)
            ->states('seller')
            ->create([
                'seller_id' => $seller->id,
            ]);
    }
}

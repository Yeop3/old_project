<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

/**
 * Class MoneyCast.
 */
final class MoneyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param Money  $value
     * @param array  $attributes
     *
     * @return Money
     */
    public function get($model, string $key, $value, array $attributes): Money
    {
        return new Money($value, new Currency('UAH'));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param Money  $value
     * @param array  $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return $value->getAmount();
    }
}

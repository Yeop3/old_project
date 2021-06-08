<?php

declare(strict_types=1);

namespace App\Casts;

use App\VO\CryptoCurrency;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CryptoCurrencyCast.
 */
final class CryptoCurrencyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return CryptoCurrency
     */
    public function get($model, string $key, $value, array $attributes): CryptoCurrency
    {
        return new CryptoCurrency($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model          $model
     * @param string         $key
     * @param CryptoCurrency $value
     * @param array          $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return $value->getValue();
    }
}

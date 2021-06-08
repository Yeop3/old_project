<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GlobalMoneyLoginDataCast.
 */
final class GlobalMoneyLoginDataCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return GlobalMoneyLoginData
     */
    public function get($model, string $key, $value, array $attributes): GlobalMoneyLoginData
    {
        return new GlobalMoneyLoginData(
            $attributes['login'],
            $attributes['password'],
            $attributes['type'],
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model                $model
     * @param string               $key
     * @param GlobalMoneyLoginData $value
     * @param array                $attributes
     *
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        return $value->jsonSerialize();
    }
}

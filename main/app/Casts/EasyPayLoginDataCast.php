<?php

namespace App\Casts;

use App\Services\Wallet\EasyPayWallet\VO\EasyPayLoginData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EasyPayLoginDataCast.
 */
class EasyPayLoginDataCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        return new EasyPayLoginData(
            $attributes['phone'],
            $attributes['password'],
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model            $model
     * @param string           $key
     * @param EasyPayLoginData $value
     * @param array            $attributes
     *
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value->jsonSerialize();
    }
}

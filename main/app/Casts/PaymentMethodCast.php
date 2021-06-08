<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Wallet\VO\PaymentMethod;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethodCast.
 */
final class PaymentMethodCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return PaymentMethod
     */
    public function get($model, string $key, $value, array $attributes): PaymentMethod
    {
        return new PaymentMethod((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model         $model
     * @param string        $key
     * @param PaymentMethod $value
     * @param array         $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

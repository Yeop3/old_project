<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Discount\VO\DiscountValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscountValueCast.
 */
final class DiscountValueCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return DiscountValue
     */
    public function get($model, string $key, $value, array $attributes): DiscountValue
    {
        return new DiscountValue((float) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model         $model
     * @param string        $key
     * @param DiscountValue $value
     * @param array         $attributes
     *
     * @return float
     */
    public function set($model, string $key, $value, array $attributes): float
    {
        return $value->getValue();
    }
}

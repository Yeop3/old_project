<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Order\VO\OrderStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderStatusCast.
 */
final class OrderStatusCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return OrderStatus
     */
    public function get($model, string $key, $value, array $attributes): OrderStatus
    {
        return new OrderStatus($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

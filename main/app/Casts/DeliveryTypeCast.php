<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\ProductType\VO\DeliveryType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeliveryTypeCast.
 */
final class DeliveryTypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return DeliveryType
     */
    public function get($model, string $key, $value, array $attributes): DeliveryType
    {
        return new DeliveryType((int) $value ?: DeliveryType::TREASURE);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model        $model
     * @param string       $key
     * @param DeliveryType $value
     * @param array        $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

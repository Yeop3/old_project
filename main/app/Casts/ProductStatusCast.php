<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Product\VO\ProductStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductStatusCast.
 */
final class ProductStatusCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return ProductStatus
     */
    public function get($model, string $key, $value, array $attributes): ProductStatus
    {
        return new ProductStatus((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model         $model
     * @param string        $key
     * @param ProductStatus $value
     * @param array         $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Proxy\VO\ProxyType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProxyTypeCast.
 */
final class ProxyTypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return ProxyType
     */
    public function get($model, string $key, $value, array $attributes): ProxyType
    {
        return new ProxyType($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model     $model
     * @param string    $key
     * @param ProxyType $value
     * @param array     $attributes
     *
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return $value->getValue();
    }
}

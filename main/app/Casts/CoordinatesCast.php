<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Coordinates\Coordinates;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class CoordinatesCast.
 */
final class CoordinatesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model       $model
     * @param string      $key
     * @param string|null $value
     * @param array       $attributes
     *
     * @return Coordinates|null
     */
    public function get($model, string $key, $value, array $attributes): ?Coordinates
    {
        try {
            return $value ? new Coordinates($value) : null;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model            $model
     * @param string           $key
     * @param Coordinates|null $value
     * @param array            $attributes
     *
     * @return string|null
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        return $value ? $value->getValue() : null;
    }
}

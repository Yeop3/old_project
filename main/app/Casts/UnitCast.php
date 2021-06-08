<?php

declare(strict_types=1);

namespace App\Casts;

use App\VO\Unit;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UnitCast.
 */
final class UnitCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model    $model
     * @param string   $key
     * @param int|null $value
     * @param array    $attributes
     *
     * @return Unit
     */
    public function get($model, string $key, $value, array $attributes): Unit
    {
        return new Unit((int) $value ?: Unit::GRAM);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model     $model
     * @param string    $key
     * @param Unit|null $value
     * @param array     $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

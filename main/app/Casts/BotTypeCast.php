<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Bot\VO\BotType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BotTypeCast.
 */
final class BotTypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return BotType
     */
    public function get($model, string $key, $value, array $attributes): BotType
    {
        return new BotType((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model   $model
     * @param string  $key
     * @param BotType $value
     * @param array   $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

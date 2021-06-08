<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Bot\VO\BotLogicType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BotLogicTypeCast.
 */
final class BotLogicTypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return BotLogicType
     */
    public function get($model, string $key, $value, array $attributes): BotLogicType
    {
        return new BotLogicType((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model        $model
     * @param string       $key
     * @param BotLogicType $value
     * @param array        $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

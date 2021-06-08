<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Bot\VO\Messenger;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessengerCast.
 */
final class MessengerCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return Messenger
     */
    public function get($model, string $key, $value, array $attributes): Messenger
    {
        return new Messenger((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model     $model
     * @param string    $key
     * @param Messenger $value
     * @param array     $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

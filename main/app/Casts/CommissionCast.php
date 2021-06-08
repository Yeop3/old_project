<?php

declare(strict_types=1);

namespace App\Casts;

use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CommissionCast.
 */
final class CommissionCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return Commission
     */
    public function get($model, string $key, $value, array $attributes): Commission
    {
        return new Commission(
            (int) $attributes['commission_value'],
            new CommissionType((int) $attributes['commission_type']),
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model      $model
     * @param string     $key
     * @param Commission $value
     * @param array      $attributes
     *
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        return [
            'commission_value' => $value->getValue(),
            'commission_type'  => $value->getType()->getValue(),
        ];
    }
}

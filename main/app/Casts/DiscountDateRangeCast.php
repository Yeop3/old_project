<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Discount\VO\DiscountDateRange;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscountDateRangeCast.
 */
final class DiscountDateRangeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param string $value
     * @param array  $attributes
     *
     * @return DiscountDateRange
     */
    public function get($model, string $key, $value, array $attributes): DiscountDateRange
    {
        return new DiscountDateRange(
            carbonSafeParse($attributes['date_start']),
            carbonSafeParse($attributes['date_end']),
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model             $model
     * @param string            $key
     * @param DiscountDateRange $value
     * @param array             $attributes
     *
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        return [
            'date_start' => $value->getStart(),
            'date_end'   => $value->getEnd(),
        ];
    }
}

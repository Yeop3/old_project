<?php

namespace App\Http\Requests\ProductType;

use App\VO\CommissionType;
use App\VO\Unit;
use Illuminate\Validation\Rule;

/**
 * Class ProductTypeRules.
 */
final class ProductTypeRules
{
    /**
     * @param $sellerId
     *
     * @return array
     */
    public static function name($sellerId): array
    {
        return ['required', 'max:255', Rule::unique('product_types')->where('seller_id', $sellerId)];
    }

    /**
     * @return string[]
     */
    public static function price(): array
    {
        return ['required', 'numeric', 'min: 1'];
    }

    /**
     * @return array
     */
    public static function comissionType(): array
    {
        return ['required', Rule::in(array_keys(CommissionType::TYPES))];
    }

    /**
     * @return string[]
     */
    public static function packing(): array
    {
        return ['nullable', 'integer', 'min:1'];
    }

    /**
     * @return array
     */
    public static function unit(): array
    {
        return ['nullable', 'integer', Rule::in(array_keys(Unit::UNITS))];
    }

    /**
     * @param $commissionType
     *
     * @return string[]
     */
    public static function comissionValue($commissionType): array
    {
        return $commissionType === CommissionType::TYPE_PERCENT
            ? ['nullable', 'integer', 'min:0', 'max:100']
            : ['nullable', 'integer', 'min:0'];
    }
}

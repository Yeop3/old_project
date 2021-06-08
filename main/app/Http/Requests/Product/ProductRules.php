<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use App\Services\Coordinates\Coordinates;
use App\Services\Coordinates\Parsers\CoordinatesParser;
use App\Services\Product\VO\ProductStatus;
use App\VO\CommissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use InvalidArgumentException;

/**
 * Class ProductRules.
 */
final class ProductRules
{
    /**
     * @param $sellerId
     *
     * @return array
     */
    public static function driverId($sellerId): array
    {
        return ['required', 'integer', Rule::exists('drivers', 'number')->where('seller_id', $sellerId)];
    }

    public static function botsNumbers(): array
    {
        return ['array'];
    }

    /**
     * @param $sellerId
     *
     * @return array
     */
    public static function botsNumbersItem($sellerId): array
    {
        return ['integer', Rule::exists('bots', 'number')->where('seller_id', $sellerId)];
    }

    /**
     * @param $sellerId
     *
     * @return array
     */
    public static function productTypeId($sellerId): array
    {
        return ['required', 'integer', Rule::exists('product_types', 'number')->where('seller_id', $sellerId)];
    }

    /**
     * @param $sellerId
     *
     * @return array
     */
    public static function localtionId($sellerId): array
    {
        return ['required', 'integer', Rule::exists('locations', 'number')->where('seller_id', $sellerId)];
    }

    public static function comissionType(): array
    {
        return ['required', Rule::in(array_keys(CommissionType::TYPES))];
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

    public static function status(): array
    {
        return ['required', Rule::in(ProductStatus::EDITABLE_STATUSES)];
    }

    public static function address(): array
    {
        return ['nullable', 'string', 'max:255'];
    }

    /**
     * @param $sellerId
     *
     * @return Unique
     */
    public static function addressArray($sellerId): Unique
    {
        return Rule::unique('products', 'address')->where('seller_id', $sellerId);
    }

    public static function images(): array
    {
        return ['required_without:video', 'array', 'min:1'];
    }

    public static function imagesItem(): array
    {
        return ['required_with:images', 'image', 'max:10000'];
    }

    public static function video(): array
    {
        return ['required_without:images', 'nullable', 'file', 'mimes:avi,mpeg,quicktime,mp4', 'max:50000'];
    }

    public static function coordinates(int $sellerId, ?int $exceptNumber = null): array
    {
        return [
            'nullable',
            'string',
            'max:255',
            function ($attribute, ?string $value, callable $fail) use ($sellerId, $exceptNumber) {
                $parser = app()->make(CoordinatesParser::class);

                $parsed = $parser->parse($value);

                if (!$parsed) {
                    $fail('Не удалось распознать координаты');

                    return;
                }

                try {
                    new Coordinates($parsed);
                } catch (InvalidArgumentException $e) {
                    $fail('Не удалось распознать координаты(ошибка)');

                    return;
                }

                $productCoordinatesExists = Product::whereSellerId($sellerId)
                    ->where('coordinates', $parsed)
                    ->when(
                        $exceptNumber,
                        fn (Builder $builder) => $builder
                        ->where('number', '!=', $exceptNumber)
                    )
                    ->exists();

                if ($productCoordinatesExists) {
                    $fail('Клад с такими координатами уже есть в системе');

                    return;
                }
            },
        ];
    }
}

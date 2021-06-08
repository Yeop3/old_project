<?php

declare(strict_types=1);

namespace App\Services\Statistic;

use App\Models\Location;
use App\Models\ProductType;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Money\Currency;
use Money\Money;

/**
 * Class StatisticQuery.
 */
class StatisticQuery
{
    /**
     * @param int                $sellerId
     * @param ProductStatus|null $status
     *
     * @return array
     */
    public function execute(int $sellerId, ?ProductStatus $status = null): array
    {
        if ($status !== null) {
            $productTypes = ProductType::with(['products' => function ($query) use ($status) {
                $query->when($status, fn (Builder $builder) => $builder->where('status', $status->getValue()))->with('location.ancestors');
            }])
                ->where('seller_id', $sellerId)
                ->whereHas(
                    'products',
                    function ($q) use ($status) {
                        $q->where('status', $status->getValue());
                    }
                )
                ->orderBy('created_at')
                ->get();
        } else {
            $productTypes = ProductType::with(['products.location.ancestors'])
                ->where('seller_id', $sellerId)
                ->orderBy('created_at')
                ->when($status, fn (Builder $builder) => $builder->where('status', $status->getValue()))
                ->get();
        }

        $headers = $productTypes->map(function (ProductType $productType) {
            return [
                'name'           => $productType->name,
                'packing'        => $productType->getPacking(),
                'products_count' => $productType->products->count(),
                'price'          => formatMoney($productType->price),
                'total'          => formatMoney($productType->price->multiply($productType->products->count())),
            ];
        });

        $locations = $productTypes->pluck('products')
            ->collapse()
            ->pluck('location')
            ->unique('name')
            ->filter()
            ->values();

        $rows = [];

        /** @var Location $location */
        foreach ($locations as $location) {
            $row = [];
            $row[0] = [
                'location_name' => $location->name_chain,
            ];

            $locationProductsCount = 0;
            $locationSum = new Money(0, new Currency('UAH'));

            /**
             * @var int         $k
             * @var ProductType $productType
             */
            foreach ($productTypes as $k => $productType) {
                $productsCount = $productType->products->where('location_id', $location->id)->count();

                $sum = $productType->price->multiply($productsCount);

                $row[$k + 1] = $productsCount;
                $locationProductsCount += $productsCount;
                $locationSum = $locationSum->add($sum);
            }

            $row[0]['total_count'] = $locationProductsCount;
            $row[0]['total_sum'] = formatMoney($locationSum);

            $rows[] = $row;
        }

        return compact('headers', 'rows');
    }
}

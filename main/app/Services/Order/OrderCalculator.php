<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Client;
use App\Models\Location;
use App\Models\Product;
use App\Services\Discount\Calculator\DiscountCalculator;
use App\Services\Discount\Calculator\DiscountCalculatorItem;
use App\Services\Order\CommissionCalculator\CommissionCalculator;
use Money\Money;

/**
 * Class OrderCalculator.
 */
final class OrderCalculator
{
    private DiscountCalculator   $discountCalculator;
    private CommissionCalculator $commissionCalculator;

    public function __construct(DiscountCalculator $discountCalculator, CommissionCalculator $commissionCalculator)
    {
        $this->discountCalculator = $discountCalculator;
        $this->commissionCalculator = $commissionCalculator;
    }

    public function calc(Product $product, Client $client): array
    {
        $product->location->loadMissing('ancestors.discounts');

        $discountItem = $this->getDiscountItem($product, $client);
        $discountAmount = $this->getDiscountAmount($product, $discountItem);

        $commission = $this->getCommission($product);

        return [
            $commission,
            $discountItem,
            $discountAmount,
            $product->productType->price
                ->multiply($product->count ?? '1')
                ->add($commission)
                ->subtract($discountAmount),
        ];
    }

    private function getDiscountItem(Product $product, Client $client): DiscountCalculatorItem
    {
        $productTypeDiscountItems = $product->productType->getDiscountCalculatorItems();
        $locationDiscountItems = $product->location->getDiscountCalculatorItems();
        $locationAncestorsDiscountItems = $product->location
            ->ancestors
            ->map(
                fn (Location $location) => $location->getDiscountCalculatorItems()
            )
            ->collapse();

        $allDiscountItems = collect([
            $productTypeDiscountItems,
            $locationDiscountItems,
            $locationAncestorsDiscountItems,
            [$client->getDiscountCalculatorItem()],
        ])
            ->collapse()
            ->toArray();

        return $this->discountCalculator->determineHighestDiscount($allDiscountItems);
    }

    private function getDiscountAmount(Product $product, DiscountCalculatorItem $discountCalculatorItem): Money
    {
        return $product->productType
            ->price
            ->multiply($product->count ?? '1')
            ->divide(100)
            ->multiply(
                $discountCalculatorItem
                ->getValue()
            );
    }

    private function getCommission(Product $product): Money
    {
        return $this->commissionCalculator->calc(
            $product->productType->price->multiply($product->count ?? '1'),
            [
                $product->productType->commission,
                $product->location->commission,
                ...$product->location->ancestors->pluck('commission')->toArray(),
                $product->commission,
            ]
        );
    }
}

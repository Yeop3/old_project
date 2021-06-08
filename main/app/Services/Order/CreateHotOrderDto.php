<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Location;
use App\Models\ProductType;
use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Source;

/**
 * Class CreateHotOrderDto.
 */
final class CreateHotOrderDto
{
    private Source $source;
    private ProductType $productType;
    private Location $location;
    private PaymentMethod $paymentMethod;
    private float $count;

    public function __construct(
        ProductType $productType,
        Location $location,
        PaymentMethod $paymentMethod,
        Source $source,
        float $count
    ) {
        $this->source = $source;
        $this->productType = $productType;
        $this->location = $location;
        $this->paymentMethod = $paymentMethod;
        $this->count = $count;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    public function getCount(): float
    {
        return $this->count;
    }
}

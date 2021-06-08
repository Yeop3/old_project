<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Location;
use App\Models\ProductType;
use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Source;

/**
 * Class CreateTaxiOrderDto.
 */
final class CreateTaxiOrderDto
{
    private Source $source;
    private string $address;
    private ProductType $productType;
    private Location $location;
    private PaymentMethod $paymentMethod;
    private float $count;
    private string $description;

    public function __construct(
        ProductType $productType,
        Location $location,
        PaymentMethod $paymentMethod,
        Source $source,
        float $count,
        string $address,
        string $description
    ) {
        $this->source = $source;
        $this->address = $address;
        $this->productType = $productType;
        $this->location = $location;
        $this->paymentMethod = $paymentMethod;
        $this->count = $count;
        $this->description = $description;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getAddress(): string
    {
        return $this->address;
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

    public function getDescription(): string
    {
        return $this->description;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\VO\Source;

/**
 * Class CreateOrderDto.
 */
final class CreateOrderDto
{
    private int $productTypeNumber;
    private ?string $paymentMethod;
    private Source $source;
    private int $locationNumber;

    public function __construct(int $productTypeNumber, int $locationNumber, ?string $paymentMethod, Source $source)
    {
        $this->productTypeNumber = $productTypeNumber;
        $this->paymentMethod = $paymentMethod;
        $this->source = $source;
        $this->locationNumber = $locationNumber;
    }

    public function getProductTypeNumber(): int
    {
        return $this->productTypeNumber;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getLocationNumber(): int
    {
        return $this->locationNumber;
    }
}

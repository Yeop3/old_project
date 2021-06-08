<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Services\Discount\VO\DiscountDateRange;
use App\Services\Discount\VO\DiscountValue;

/**
 * Class DiscountDto.
 */
final class DiscountDto
{
    private string $name;
    private DiscountValue $discountValue;
    private int $discountPriority;
    private array $locationNumbers;
    private array $productTypeNumbers;
    private DiscountDateRange $dateRange;
    private int $clientMinPaidOrdersCount;
    private int $clientMinIncome;
    private ?string $description;
    private bool $active;

    public function __construct(
        string $name,
        DiscountValue $discountValue,
        int $discountPriority,
        bool $active,
        array $locationNumbers,
        array $productTypeNumbers,
        DiscountDateRange $dateRange,
        int $clientMinPaidOrdersCount,
        int $clientMinIncome,
        ?string $description = null
    ) {
        $this->name = $name;
        $this->discountValue = $discountValue;
        $this->discountPriority = $discountPriority;
        $this->locationNumbers = $locationNumbers;
        $this->productTypeNumbers = $productTypeNumbers;
        $this->dateRange = $dateRange;
        $this->clientMinPaidOrdersCount = $clientMinPaidOrdersCount;
        $this->clientMinIncome = $clientMinIncome;
        $this->description = $description;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDiscountValue(): DiscountValue
    {
        return $this->discountValue;
    }

    public function getDiscountPriority(): int
    {
        return $this->discountPriority;
    }

    public function getLocationNumbers(): array
    {
        return $this->locationNumbers;
    }

    public function getProductTypeNumbers(): array
    {
        return $this->productTypeNumbers;
    }

    public function getDateRange(): DiscountDateRange
    {
        return $this->dateRange;
    }

    public function getClientMinPaidOrdersCount(): int
    {
        return $this->clientMinPaidOrdersCount;
    }

    public function getClientMinIncome(): int
    {
        return $this->clientMinIncome;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}

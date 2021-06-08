<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;

/**
 * Class ProductMassDto.
 */
final class ProductMassDto
{
    private int $driverId;
    private int $productTypeId;
    private int $locationId;
    private Commission $commission;
    private ?array $addresses;
    private ProductStatus $status;
    private ?array $botsNumbers;
    private array $images;
    private array $coordinates;

    public function __construct(int $driverId, int $productTypeId, int $locationId, Commission $commission, array $coordinates, array $images, ?array $addresses = null, ProductStatus $status, ?array $botsNumbers = null)
    {
        $this->driverId = $driverId;
        $this->productTypeId = $productTypeId;
        $this->locationId = $locationId;
        $this->commission = $commission;
        $this->addresses = $addresses;
        $this->status = $status;
        $this->botsNumbers = $botsNumbers;
        $this->images = $images;
        $this->coordinates = $coordinates;
    }

    /**
     * @return array|null
     */
    public function getBotsNumbers(): ?array
    {
        return $this->botsNumbers;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function getProductTypeId(): int
    {
        return $this->productTypeId;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getCommission(): Commission
    {
        return $this->commission;
    }

    public function getAddresses(): array
    {
        return $this->addresses ?? [];
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use Illuminate\Http\UploadedFile;

/**
 * Class ProductDto.
 */
final class ProductDto
{
    private int $driverNumber;
    private int $productTypeNumber;
    private int $locationNumber;
    private Commission $commission;
    private ?string $address;
    private ProductStatus $status;
    private ?array $botsNumbers;
    private ?array $images;
    private ?UploadedFile $video;
    private ?string $coordinates;
    private ?int $telegramId;

    public function __construct(
        int $driverNumber,
        int $productTypeNumber,
        int $locationNumber,
        Commission $commission,
        ?string $coordinates,
        ?array $images,
        ProductStatus $status,
        ?UploadedFile $video,
        ?string $address = null,
        ?array $botsNumbers = null,
        ?int $telegramId = null
    ) {
        $this->driverNumber = $driverNumber;
        $this->productTypeNumber = $productTypeNumber;
        $this->locationNumber = $locationNumber;
        $this->commission = $commission;
        $this->address = $address;
        $this->status = $status;
        $this->botsNumbers = $botsNumbers;
        $this->images = $images;
        $this->video = $video;
        $this->coordinates = $coordinates;
        $this->telegramId = $telegramId;
    }

    /**
     * @return array|null
     */
    public function getBotsNumbers(): ?array
    {
        return $this->botsNumbers;
    }

    public function getDriverNumber(): int
    {
        return $this->driverNumber;
    }

    public function getProductTypeNumber(): int
    {
        return $this->productTypeNumber;
    }

    public function getLocationNumber(): int
    {
        return $this->locationNumber;
    }

    public function getCommission(): Commission
    {
        return $this->commission;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function getVideo(): ?UploadedFile
    {
        return $this->video;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }
}

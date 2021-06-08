<?php

declare(strict_types=1);

namespace App\Services\Product\Index;

/**
 * Class IndexProductDto.
 */
final class IndexProductDto
{
    private ?int $location_id;
    private ?int $driver_id;
    private ?int $product_type_id;
    private ?string $address;
    private ?int $number;
    private ?int $status;
    private ?string $sortField;
    private ?string $sortDirection;
    private ?string $coordinates;
    private ?int $comission;
    private ?string $created_at;

    /**
     * IndexProductDto constructor.
     *
     * @param int|null    $location_id
     * @param int|null    $driver_id
     * @param int|null    $product_type_id
     * @param string|null $address
     * @param int|null    $number
     * @param int|null    $status
     * @param string|null $sortField
     * @param string|null $sortDirection
     * @param string|null $coordinates
     * @param int|null    $comission
     * @param string|null $created_at
     */
    public function __construct(
        ?int $location_id,
        ?int $driver_id,
        ?int $product_type_id,
        ?string $address,
        ?int $number,
        ?int $status,
        ?string $sortField,
        ?string $sortDirection,
        ?string $coordinates,
        ?int $comission,
        ?string $created_at
    ) {
        $this->location_id = $location_id;
        $this->driver_id = $driver_id;
        $this->product_type_id = $product_type_id;
        $this->address = $address;
        $this->number = $number;
        $this->status = $status;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->coordinates = $coordinates;
        $this->comission = $comission;
        $this->created_at = $created_at;
    }

    /**
     * @return int|null
     */
    public function getDriverId(): ?int
    {
        return $this->driver_id;
    }

    /**
     * @return int|null
     */
    public function getLocationId(): ?int
    {
        return $this->location_id;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getProductTypeId(): ?int
    {
        return $this->product_type_id;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    /**
     * @return string|null
     */
    public function getSortDirection(): ?string
    {
        return $this->sortDirection;
    }

    /**
     * @return string|null
     */
    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    /**
     * @return int|null
     */
    public function getComission(): ?int
    {
        return $this->comission;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
}

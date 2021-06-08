<?php

declare(strict_types=1);

namespace App\Services\Location;

use App\VO\Commission;

/**
 * Class LocationDto.
 */
final class LocationDto
{
    private string $name;
    private int $priority;
    private bool $isBranch;
    private Commission $commission;
    private ?int $parentNumber;
    private array $driver_numbers;

    public function __construct(string $name, int $priority, bool $isBranch, Commission $commission, array $driver_numbers, ?int $parentNumber = null)
    {
        $this->name = $name;
        $this->priority = $priority;
        $this->isBranch = $isBranch;
        $this->commission = $commission;
        $this->driver_numbers = $driver_numbers;
        $this->parentNumber = $parentNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isBranch(): bool
    {
        return $this->isBranch;
    }

    public function getCommission(): Commission
    {
        return $this->commission;
    }

    public function getParentNumber(): ?int
    {
        return $this->parentNumber;
    }

    public function getDriverNumbers(): array
    {
        return $this->driver_numbers;
    }
}

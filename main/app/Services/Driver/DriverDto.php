<?php

declare(strict_types=1);

namespace App\Services\Driver;

use App\Services\Driver\VO\PermissionTypes;

/**
 * Class DriverDto.
 */
final class DriverDto {

    private string $name;
    private int    $clientNumber;
    private array  $locationNumbers;
    private ?array $permissions;

    /**
     * ProductTypeDto constructor.
     *
     * @param string $name
     * @param int $clientNumber
     * @param PermissionTypes[] $permissions
     * @param int[]|null $locationNumbers
     */
    public function __construct(
        string $name,
        int $clientNumber,
        array $locationNumbers = null,
        ?array $permissions = null

    )
    {
        $this->name            = $name;
        $this->clientNumber    = $clientNumber;
        $this->locationNumbers = $locationNumbers ?? [];
        $this->permissions     = $permissions ?? [];

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClientNumber(): int
    {
        return $this->clientNumber;
    }

    /**
     * @return PermissionTypes[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return array|int[]
     */
    public function getLocationNumbers(): array
    {
        return $this->locationNumbers;
    }
}

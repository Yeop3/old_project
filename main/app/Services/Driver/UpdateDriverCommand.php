<?php

declare(strict_types=1);

namespace App\Services\Driver;

use App\Models\Client;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Seller;
use App\Services\Driver\VO\PermissionTypes;

/**
 * Class UpdateDriverCommand.
 */
final class UpdateDriverCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $driverNumber, Seller $seller, DriverDto $dto): Driver
    {
        $this->checker->checkDuplicate($seller, $dto, $driverNumber);

        $driver = Driver::whereSellerId($seller->id)->whereNumber($driverNumber)->firstOrFail();

        $client = Client::whereSellerId($seller->id)->whereNumber($dto->getClientNumber())->first();

        $driver->name = $dto->getName();
        $driver->client_id = $client->id ?? null;
        $driver->telegram_id = $client->telegram_id ?? null;
        $driver->permissions = array_map(
            fn (PermissionTypes $permissionTypes) => $permissionTypes->getValue(),
            $dto->getPermissions()
        );

        $locationIds = $dto->getLocationNumbers()
            ? Location::whereSellerId($seller->id)
                ->whereIn(
                    'number',
                    $dto
                        ->getLocationNumbers()
                )->pluck('id')
            : [];

        $driver->save();

        $driver->locations()->sync($dto->getLocationNumbers());

        return $driver;
    }
}

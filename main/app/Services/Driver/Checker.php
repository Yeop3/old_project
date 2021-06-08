<?php

declare(strict_types=1);

namespace App\Services\Driver;

use App\Models\Driver;
use App\Models\Seller;
use App\Services\Driver\Exceptions\DriverExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkDuplicate(Seller $seller, DriverDto $dto, ?int $exceptNumber = null): void
    {
        $name = $dto->getName();

        $driverExists = Driver::whereSellerId($seller->id)
            ->whereName($name)
            ->when($exceptNumber, fn (Builder $query) => $query->where('number', '!=', $exceptNumber))
            ->exists();

        if ($driverExists) {
            throw new DriverExistsException("Driver with name $name already exists");
        }
    }
}

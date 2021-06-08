<?php

declare(strict_types=1);

namespace App\Services\Location;

use App\Models\Driver;
use App\Models\Location;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateLocationCommand.
 */
final class UpdateLocationCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $locationNumber, Seller $seller, LocationDto $dto): Location
    {
        $location = Location::whereSellerId($seller->id)->whereNumber($locationNumber)->firstOrFail();

        $parentLocation = $location->parent;

        $this->checker->checkDuplicate($seller, $dto, $parentLocation->id ?? null, $locationNumber);

        $location->name = $dto->getName();
        $location->priority = $dto->getPriority();
        $location->is_branch = $dto->isBranch();
        $location->commission = $dto->getCommission();

        $driverIds = $dto->getDriverNumbers()
            ? Driver::whereSellerId($seller->id)
                ->whereIn(
                    'number',
                    $dto
                    ->getDriverNumbers(

                    )
                )->pluck('id')
            : [];

        $location->save();

        DB::beginTransaction();

        $location->save();

        $location->drivers()->sync($driverIds);

        DB::commit();

        return $location;
    }
}

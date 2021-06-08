<?php

declare(strict_types=1);

namespace App\Services\Location;

use App\Models\Driver;
use App\Models\Location;
use App\Models\Seller;
use App\Services\Location\Exceptions\LocationLevelException;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateLocationCommand.
 */
final class CreateLocationCommand {

    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(Seller $seller, LocationDto $dto): Location
    {
        $parentLocation = null;

        if($dto->getParentNumber()){
            $parentLocation = Location::whereSellerId(
                $seller->id
            )->whereNumber(
                $dto->getParentNumber()
            )->firstOrFail();

            $this->checker->checkProductParent($parentLocation);
        }



        if (
            $parentLocation instanceof Location &&
            $dto->getParentNumber() &&
            $parentLocation->getAttribute('parent_id')
        )
        {
            throw new LocationLevelException(
                'Данная локация не может являтся дочерней для выбранного родителя.'
            );
        }

        $this->checker->checkDuplicate($seller, $dto, $parentLocation->id ?? null);

        $location = new Location();

        $location->seller_id  = $seller->id;
        $location->name       = $dto->getName();
        $location->priority   = $dto->getPriority();
        $location->is_branch  = $dto->isBranch();
        $location->commission = $dto->getCommission();
        $location->parent_id  = optional($parentLocation)->id;

        $driverIds = $dto->getDriverNumbers()
            ? Driver::whereSellerId(
                $seller->id
            )->whereIn('number', $dto->getDriverNumbers())->pluck('id')
            : [];

        DB::beginTransaction();

        $location->save();

        $location->drivers()->sync($driverIds);

        DB::commit();

        return $location;
    }
}

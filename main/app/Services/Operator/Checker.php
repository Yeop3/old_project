<?php

declare(strict_types=1);

namespace App\Services\Operator;

use App\Models\Operator;
use App\Models\Seller;
use App\Services\Operator\Exceptions\OperatorExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkDuplicate(Seller $seller, OperatorDto $dto, ?int $exceptNumber = null): void
    {
        $name = $dto->getName();

        $driverExists = Operator::whereSellerId($seller->id)
            ->whereName($name)
            ->when($exceptNumber, fn (Builder $query) => $query->where('number', '!=', $exceptNumber))
            ->exists();

        if ($driverExists) {
            throw new OperatorExistsException("Driver with name $name already exists");
        }
    }
}

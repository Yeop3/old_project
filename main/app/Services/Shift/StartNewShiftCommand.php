<?php

declare(strict_types=1);

namespace App\Services\Shift;

use App\Models\Operator;
use App\Models\Seller;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

/**
 * Class StartNewShiftCommand.
 */
final class StartNewShiftCommand
{
    public function execute(Seller $seller, int $operatorNumber): Shift
    {
        $operator = Operator::whereSellerId($seller->id)->whereNumber($operatorNumber)->firstOrFail();

        $shift = new Shift();
        $shift->seller_id = $seller->id;
        $shift->operator_id = $operator->id;

        DB::beginTransaction();

        $this->endShift($seller->shifts()->current()->first());

        $this->startShift($shift);

        DB::commit();

        return $shift;
    }

    private function startShift(Shift $shift): void
    {
        $shift->started_at = now();
        $shift->ended_at = null;

        $shift->save();
    }

    private function endShift(Shift $shift): void
    {
        $shift->ended_at = now();

        $shift->save();
    }
}

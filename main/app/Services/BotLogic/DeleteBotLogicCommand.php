<?php

declare(strict_types=1);

namespace App\Services\BotLogic;

use App\Models\Bot;
use App\Models\BotLogic\BotLogic;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

/**
 * Class DeleteBotLogicCommand.
 */
final class DeleteBotLogicCommand
{
    public function execute(int $botLogicNumber, Seller $seller): void
    {
        $logic = BotLogic::whereSellerId($seller->id)
            ->whereNumber($botLogicNumber)
            ->firstOrFail();

        $botsUsedLogic = Bot::whereLogicId($logic->id)
            ->whereSellerId($seller->id)
            ->get();

        DB::beginTransaction();

        if ($botsUsedLogic->count()) {
            $botsUsedLogic->map(function (Bot $bot) {
                $bot->logic_id = 1;
                $bot->save();
            });
        }

        $logic->delete();

        DB::commit();
    }
}

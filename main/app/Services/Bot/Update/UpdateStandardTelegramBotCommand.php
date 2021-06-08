<?php

declare(strict_types=1);

namespace App\Services\Bot\Update;

use App\Models\Bot;
use App\Models\BotLogic\BotLogic;
use App\Models\Driver;
use App\Models\Operator;
use App\Models\Seller;
use App\Services\Bot\Checker;
use App\Services\Bot\VO\BotLogicType;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateStandardTelegramBotCommand.
 */
final class UpdateStandardTelegramBotCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $botNumber, Seller $seller, UpdateStandardTelegramBotDto $dto): Bot
    {
        $this->checker->checkNameDuplicate($seller, $dto->getName(), $botNumber);

        $sellerIdForBotLogic = $dto->getLogicType()->getValue() === BotLogicType::STANDARD
            ? null
            : $seller->id;

        $logic = BotLogic::whereSellerId($sellerIdForBotLogic)->whereNumber($dto->getLogicNumber())->firstOrFail();

        $operator = Operator::whereSellerId($seller->id)->whereNumber($dto->getOperatorNumber())->first();

        $bot = Bot::whereSellerId($seller->id)->whereNumber($botNumber)->firstOrFail();

        $bot->name = $dto->getName();
        $bot->logic_id = $logic->id;
        $bot->operator_id = $operator->id ?? null;
        $bot->active = $dto->isActive();
        $bot->allow_create_clients = $dto->isAllowCreateClients();

        $driverIds = $dto->getDriverNumbers()
            ? Driver::whereSellerId($seller->id)->whereIn('number', $dto->getDriverNumbers())->pluck('id')
            : [];

        DB::beginTransaction();

        $bot->save();

        $bot->drivers()->sync($driverIds);

        DB::commit();

        return $bot;
    }
}

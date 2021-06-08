<?php

declare(strict_types=1);

namespace App\Services\Bot\Create;

use App\Bot\Commands\GetTelegramBotInfoCommand;
use App\Bot\Register\RegisterTelegramBotCommand;
use App\Models\Bot;
use App\Models\BotLogic\BotLogic;
use App\Models\Driver;
use App\Models\Operator;
use App\Models\Seller;
use App\Services\Bot\Checker;
use App\Services\Bot\Exceptions\CantRegisterTelegramBotException;
use App\Services\Bot\VO\BotLogicType;
use App\Services\Bot\VO\BotType;
use App\Services\Bot\VO\Messenger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class CreateStandardTelegramBotCommand.
 */
final class CreateStandardTelegramBotCommand
{
    private GetTelegramBotInfoCommand $getTelegramBotInfoCommand;
    private RegisterTelegramBotCommand $registerTelegramBotCommand;
    private Checker $checker;

    public function __construct(RegisterTelegramBotCommand $registerTelegramBotCommand, Checker $checker, GetTelegramBotInfoCommand $getTelegramBotInfoCommand)
    {
        $this->registerTelegramBotCommand = $registerTelegramBotCommand;
        $this->checker = $checker;
        $this->getTelegramBotInfoCommand = $getTelegramBotInfoCommand;
    }

    public function execute(Seller $seller, CreateStandardTelegramBotDto $dto): Bot
    {
        $this->checker->checkNameDuplicate($seller, $dto->getName());

        $sellerIdForBotLogic = $dto->getLogicType()->getValue() === BotLogicType::STANDARD
            ? null
            : $seller->id;

        $logic = BotLogic::whereSellerId($sellerIdForBotLogic)->whereNumber($dto->getLogicNumber())->firstOrFail();
        $operator = Operator::whereSellerId($seller->id)->whereNumber($dto->getOperatorNumber())->first();

        $bot = new Bot();

        $botInfo = $this->getTelegramBotInfoCommand->execute($dto->getToken());

        $bot->name = $dto->getName();
        $bot->username = $botInfo['result']['username'];
        $bot->token = $dto->getToken();
        $bot->slug = Str::random();
        $bot->messenger = new Messenger(Messenger::TELEGRAM);
        $bot->type = new BotType(BotType::STANDARD);
        $bot->logic_id = $logic->id;
        $bot->active = $dto->isActive();
        $bot->allow_create_clients = $dto->isAllowCreateClients();
        $bot->operator_id = $operator->id ?? null;
        $bot->seller_id = $seller->id;

        $registerResult = $this->registerTelegramBotCommand->execute($bot->token, $bot->getWebHookUrl());

        if (!$registerResult['ok']) {
            throw new CantRegisterTelegramBotException($registerResult['description']);
        }

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

<?php

declare(strict_types=1);

namespace App\Services\Bot\Delete;

use App\Bot\Delete\DeleteTelegramBotCommand;
use App\Models\Bot;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

/**
 * Class DeleteStandardTelegramBotCommand.
 */
final class DeleteStandardTelegramBotCommand
{
    private DeleteTelegramBotCommand $deleteTelegramBotCommand;

    public function __construct(DeleteTelegramBotCommand $deleteTelegramBotCommand)
    {
        $this->deleteTelegramBotCommand = $deleteTelegramBotCommand;
    }

    public function execute(int $botNumber, Seller $seller): void
    {
        $bot = Bot::whereSellerId($seller->id)->whereNumber($botNumber)->firstOrFail();

        DB::beginTransaction();

        $bot->delete();

        $this->deleteTelegramBotCommand->execute($bot->token);

        DB::commit();
    }
}

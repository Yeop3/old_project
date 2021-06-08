<?php

declare(strict_types=1);

namespace App\MainBot\Handlers;

use App\Models\MainBot;
use BotMan\BotMan\BotMan;

/**
 * Class AliveHandler.
 */
final class AliveHandler implements Handler
{
    public function execute(BotMan $botMan, MainBot $botModel, ...$params): void
    {
        $botMan1 = $botMan;

        $template = config('main_bot_logic.standart.commands.alive.content');

        $botMan1->reply($template);
    }
}

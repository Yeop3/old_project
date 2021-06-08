<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use BotMan\BotMan\BotMan;

/**
 * Interface BotHandler.
 */
interface BotHandler
{
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void;
}

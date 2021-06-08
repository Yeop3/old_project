<?php

declare(strict_types=1);

namespace App\MainBot\Handlers;

use App\Models\MainBot;
use BotMan\BotMan\BotMan;

/**
 * Interface Handler.
 */
interface Handler
{
    public function execute(BotMan $botMan, MainBot $botModel, ?string ...$params): void;
}

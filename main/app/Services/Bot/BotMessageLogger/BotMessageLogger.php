<?php

declare(strict_types=1);

namespace App\Services\Bot\BotMessageLogger;

use App\Models\Client;

/**
 * Interface BotMessageLogger.
 */
interface BotMessageLogger
{
    public function log(Client $client, string $text, bool $fromBot): void;
}

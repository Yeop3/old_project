<?php

declare(strict_types=1);

namespace App\Bot\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;

/**
 * Class HandleTextMiddleware.
 */
final class HandleTextMiddleware implements Sending
{
    /**
     * Handle an outgoing message payload before/after it
     * hits the message service.
     *
     * @param mixed    $payload
     * @param callable $next
     * @param BotMan   $bot
     *
     * @return mixed
     */
    public function sending($payload, $next, BotMan $bot)
    {
        if (isset($payload['text'])) {
            $payload['text'] = str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $payload['text']);
        }

        if (isset($payload['caption'])) {
            $payload['caption'] = str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $payload['caption']);
        }

        $payload['parse_mode'] = 'HTML';

        return $next($payload);
    }
}

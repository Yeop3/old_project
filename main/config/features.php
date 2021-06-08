<?php

declare(strict_types=1);

return [
    'bot_callback_rate_limit' => [
        'enable' => env('BOT_CALLBACK_RATE_LIMIT_ENABLE', true),
        'ttl'    => env('BOT_CALLBACK_RATE_LIMIT_TTL', 1),
    ],
];

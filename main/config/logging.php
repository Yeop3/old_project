<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'single'),

    'telegram_bot_token' => env('ERROR_LOG_TELEGRAM_BOT_TOKEN'),
    'telegram_id'        => env('ERROR_LOG_TELEGRAM_ID'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'orders' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/orders/errors.log'),
            'level'  => 'debug',
        ],
        'conversations' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/conversations/conversations.log'),
            'level'  => 'debug',
        ],
        'taxi_orders' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/orders/taxi_errors.log'),
            'level'  => 'debug',
        ],

        'hot_orders' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/orders/hot_errors.log'),
            'level'  => 'debug',
        ],

        'bitaps_callbacks' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/bitaps/callbacks.log'),
            'level'  => 'debug',
        ],

        'crypto_checks' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/crypto/checks.log'),
            'level'  => 'debug',
        ],

        'global_money_card_checks' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/global_money/card_checks.log'),
            'level'  => 'debug',
        ],

        'easy_pay_checks' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/easy_pay/card_checks.log'),
            'level'  => 'debug',
        ],

        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 14,
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => 'debug',
            'handler'      => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];

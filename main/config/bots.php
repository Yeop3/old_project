<?php

declare(strict_types=1);

return [
    'telegram' => [
        'test_token'                       => env('TELEGRAM_BOT_TEST_TOKEN'),
        'test_main_token'                  => env('TELEGRAM_MAIN_BOT_TEST_TOKEN'),
        'test_main_seller_create_password' => env('TELEGRAM_MAIN_BOT_TEST_SELLER_CREATE_PASSWORD'),
    ],
];

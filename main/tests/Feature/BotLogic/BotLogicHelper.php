<?php

declare(strict_types=1);

namespace Tests\Feature\BotLogic;

final class BotLogicHelper
{
    public const LOGIC_JSON_STRUCTURE = [
        'name',
        'description',
        'commands' => [
            [
                'title',
                'keys',
                'templates' => [
                    [
                        'key',
                        'title',
                        'description',
                        'tab',
                        'content',
                    ],
                ],
            ],
        ],
        'events' => [
            [
                'key',
                'title',
                'description',
                'tab',
                'content',
            ],
        ],
        'operator_notifications' => [
            [
                'key',
                'title',
                'description',
                'tab',
                'content',
            ],
        ],
        'antispams' => [
            [
                'key',
                'title',
                'options' => [
                    [
                        'key',
                        'title',
                        'description',
                        'tab',
                        'value',
                    ],
                ],
            ],
        ],
        'reminders' => [
            [
                'key',
                'title',
                'options' => [
                    [
                        'key',
                        'title',
                        'description',
                        'tab',
                        'value',
                    ],
                ],
            ],
        ],
        'distributions' => [
            [
                'key',
                'title',
                'description',
                'tab',
                'content',
            ],
        ],
    ];
}

<?php

declare(strict_types=1);

return [
    'standart' => [
        'name'        => 'Main bot',
        'description' => 'Стандартная логика',
        'commands'    => [
            'list_sellers' => [
                'key'         => 'list_sellers',
                'title'       => 'Доступные продавцы',
                'description' => 'Показ всех продавцов при вводе любой комманды',
                'content'     => '<b>Доступные продавцы:</b>{list}',
            ],
            'list_sellers_not_found' => [
                'key'         => 'list_sellers_not_found',
                'title'       => 'Вариант, когда продавцы отсутсвуют',
                'description' => null,
                'content'     => '❗️ <b>К сожалению сейчас нет доступных продавцов, зайдите позже.</b>',
            ],
            'alive' => [
                'key'         => 'alive',
                'title'       => 'Проверка бота на живучесть',
                'description' => null,
                'content'     => 'yes',
            ],
            'seller_create' => [
                'key'         => 'seller_create',
                'title'       => 'Создание продавца',
                'description' => null,
                'content'     => 'Создание продавца',
            ],
        ],
    ],
];

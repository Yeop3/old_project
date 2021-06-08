<?php

declare(strict_types=1);

return [
    'standard' => [
        'name'        => 'Telegram',
        'description' => 'Базовая системная логика со ссылочными командами вида "/command" для стандартных Telegram ботов.',

        'commands' => [
            [
                'keys'      => ['/menu', '0', 'ANYKEY'],
                'title'     => 'Главное меню',
                'templates' => [
                    [
                        'key'         => 'menu',
                        'title'       => 'Главное меню бота',
                        'description' => 'Вариант оформления главного меню бота. Вызывается с помощью комманды /menu либо при вводе любого текста кроме комманд. Главное меню недоступно когда у клиента имеется заказ в статусе «Ожидает оплаты» или «Частично оплачен».',
                        'content'     => '<b>Вас приветствует бот</b>
                            Оператор {operator}
                            Выберите раздел

                            📦 Товары и цены
                            <i>Жми</i> 👉 /products
                            - - - - - - - - - - - - - - - -
                            🌆 Выбрать район
                            <i>Жми</i> 👉 /locations
                            - - - - - - - - - - - - - - - -
                            💰 Мой последний заказ
                            <i>Жми</i> 👉 /last_order',
                    ],
                ],
            ],

            [
                'keys'      => ['3231', '23232'],
                'title'     => 'меню',
                'templates' => [
                    [
                        'key'         => 'qqq',
                        'title'       => 'Главное меню бота',
                        'description' => 'Вариант оформления главного меню бота. Вызывается с помощью комманды /menu либо при вводе любого текста кроме комманд. Главное меню недоступно когда у клиента имеется заказ в статусе «Ожидает оплаты» или «Частично оплачен».',
                        'content'     => '<b>Вас приветствует бот</b>
                            Оператор {operator}
                            Выберите раздел

                            📦 Товары и цены
                            <i>Жми</i> 👉 /products
                            - - - - - - - - - - - - - - - -
                            🌆 Выбрать район
                            <i>Жми</i> 👉 /locations
                            - - - - - - - - - - - - - - - -
                            💰 Мой последний заказ
                            <i>Жми</i> 👉 /last_order',
                    ],
                ],
            ],

            [
                'keys'      => ['/products', '1'],
                'title'     => 'Ассортимент товаров',
                'templates' => [
                    [
                        'key'         => 'products',
                        'title'       => 'Товары в наличии',
                        'description' => 'Список товаров. Вызывается с помощью комманды /products. Выводит перечень типов товаров, которые есть в наличии с сортировкой по приоритету.',
                        'content'     => '<b>Выберите товар</b>

                            {list}

                            ',
                    ],
                    [
                        'key'         => 'products_absent',
                        'title'       => 'Вариант, когда товары отсутствуют',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            но у нас все товары временно закончились.
                            Попробуйте зайти позже.

                            ',
                    ],
                    [
                        'key'         => 'product',
                        'title'       => 'Шаблон каждого типа товара',
                        'description' => null,
                        'content'     => '📦 {item-product_type-name} ({item-product_type-packing} {item-product_type-unit}){item-product_type-discount}
                            {item-product_type-price} 👉 /product_{item-product_type-number}',
                    ],
                    [
                        'key'         => 'product_divider',
                        'title'       => 'Разделитель между товарами',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'product_discount',
                        'title'       => 'Шаблон информации о скидке',
                        'description' => null,
                        'content'     => "\n<b>+ скидка до {percent}%</b>",
                    ],
                ],
            ],

            [
                'keys'      => ['/locations', '2'],
                'title'     => 'Основные локации',
                'templates' => [
                    [
                        'key'         => 'locations',
                        'title'       => 'Основные локации',
                        'description' => 'Список основных локаций, в которых есть доступные для заказа товары. Вызывается с помощью комманды /locations.',
                        'content'     => '<b>Выберите район</b>

                            {list}

                           ',
                    ],
                    [
                        'key'         => 'locations_products_absent',
                        'title'       => 'Вариант, когда товары отсутствуют',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            но все товары раскупили по всем районам!
                            Попробуйте зайти позже.

                            ',
                    ],
                    [
                        'key'         => 'location',
                        'title'       => 'Шаблон каждой локации',
                        'description' => null,
                        'content'     => '🚩 <i>{item-location-name}</i>{item-location-discount}
                          <i>Жми</i> 👉 /location_{item-location-number}',
                    ],
                    [
                        'key'         => 'location_divider',
                        'title'       => 'Разделитель между локациями',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'location_discount',
                        'title'       => 'Шаблон информации о скидке',
                        'description' => null,
                        'content'     => "\n<b>+ скидка до {percent}%</b>",
                    ],
                ],
            ],

            [
                'keys'      => ['/location_{ID}', '2{ID}'],
                'title'     => 'Выбрать локацию',
                'templates' => [
                    [
                        'key'         => 'location',
                        'title'       => 'Товары в наличии при выбранной конечной локации',
                        'description' => 'Выводит перечень типов товаров, которые есть в наличии в выбранной локации с сортировкой по приоритету если выбранная локация является конечной. Вызывается с помощью комманды /location_ID, где ID - номер локации.',
                        'content'     => '<b>Выберите товар</b>
                            в районе {location-name}

                            {list}

                            ',
                    ],
                    [
                        'key'         => 'location_products_absent',
                        'title'       => 'Вариант, когда в выбранной конечной локации отсутствуют товары',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            все товары в районе {location-name} уже раскупили.

                            Другой район
                            <i>Жми</i> 👉 /locations

                            ',
                    ],
                    [
                        'key'         => 'location_product',
                        'title'       => 'Шаблон каждого типа товара в конечной локации',
                        'description' => null,
                        'content'     => '📦 {item-product_type-name} ({item-product_type-packing} {item-product_type-unit})
                            <b>{item-product_type-price}</b>{item-product_type-discount}
                            <i>Заказать</i> 👉 /order_{item-product_type-number}_{location-number}',
                    ],
                    [
                        'key'         => 'location_product_divider',
                        'title'       => 'Разделитель между товарами в конечной локации',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'location_children',
                        'title'       => 'Вариант, когда есть дочерние локации',
                        'description' => 'Список дочерних локаций - выдается, если в выбранной локации есть дочерние локации с доступными для покупки товарами. Сортировка по приоритету',
                        'content'     => '<b>{location-name}</b>
                            Уточните район:

                            {list}

                            ',
                    ],
                    [
                        'key'         => 'location_children_products_absent',
                        'title'       => 'Вариант, когда в дочерних локациях нет товаров',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            в районе {location-name} товары уже закончились.

                           ',
                    ],
                    [
                        'key'         => 'location_child',
                        'title'       => 'Шаблон каждой дочерней локации',
                        'description' => null,
                        'content'     => '🏘 <i>{item-location-name}</i>{item-location-discount}
                          <i>Жми</i> 👉 /location_{item-location-number}',
                    ],
                    [
                        'key'         => 'location_children_divider',
                        'title'       => 'Разделитель между дочерними локациями',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'location_discount',
                        'title'       => 'Шаблон информации о скидке',
                        'description' => null,
                        'content'     => "\n<b>+ скидка до {percent}%</b>",
                    ],
                    [
                        'key'         => 'location_absent',
                        'title'       => 'Вариант, когда клиент запросил несуществующую локацию',
                        'description' => null,
                        'content'     => '❗️ Район удален или не существует

                           ',
                    ],
                ],
            ],

            [
                'keys'      => ['/product_{ID}', '1{ID}'],
                'title'     => 'Выбрать товар',
                'templates' => [
                    [
                        'key'         => 'product_locations',
                        'title'       => 'Основные локации, в которых продается выбранный товар',
                        'description' => 'Выводит список локаций с сортировкой по приоритету, в которых продается выбранный товар. Вызывается с помощью комманды /product_ID, где ID - номер типа товара.',
                        'content'     => '<b>Вы заказываете
                            {product_type-name} за {product_type-price}</b>
                            Уточните район:

                            {list}

                           ',
                    ],
                    [
                        'key'         => 'product_absent',
                        'title'       => 'Вариант, когда выбранный товар везде закончился',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            {product_type-name} закончился.
                            <i>Выбрать другой товар</i> 👉 /products

                            ',
                    ],
                    [
                        'key'         => 'product_location',
                        'title'       => 'Шаблон каждой локации',
                        'description' => null,
                        'content'     => '🚩 <i>{item-location-name}</i>{item-location-discount}
                            <i>Далее</i> 👉 /order_{product_type-number}_{item-location-number}',
                    ],
                    [
                        'key'         => 'product_location_divider',
                        'title'       => 'Разделитель между локациями',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'product_location_discount',
                        'title'       => 'Шаблон информации о скидке',
                        'description' => null,
                        'content'     => "\n<b>+ скидка до {percent}%</b>",
                    ],
                    [
                        'key'         => 'product_location_not_found',
                        'title'       => 'Вариант, когда клиент запросил несуществующий товар',
                        'description' => null,
                        'content'     => '❗️ Товар удален или не существует',
                    ],
                ],
            ],

            [
                'keys' => [
                    '/order_[Location:ID]_[product_type:ID]',
                    '30[Location:ID]_[product_type:ID]',
                    '/order_[Location:ID]_[product_type:ID]_[PayMethod]',
                    '/31[Location:ID]_[product_type:ID]_[PayMethod]',
                ],
                'title'     => 'Заказать товар',
                'templates' => [
                    [
                        'key'         => 'order_qiwi_3',
                        'title'       => 'Товар заказан (QIWI, ручной кошелек)',
                        'description' => 'Выводит информацию о новом заказе (QIWI с ручным кошельком)',
                        'tab'         => 'QIWI 3',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Переведите на QIWI-кошелек
                            <b>{order-purse-phone}</b>
                            сумму <b>{order-amount-unpaid}</b>.
                            В комментарии к платежу ничего указывать не нужно.
                            <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                            <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.
                           ',
                    ],
                    [
                        'key'         => 'order_crypto_btc',
                        'title'       => 'Товар заказан (BTC)',
                        'description' => 'Выводит информацию о новом заказе (BTC)',
                        'tab'         => 'Bitcoin (BTC)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Переведите на адрес <b>Bitcoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>

                           ',
                    ],
                    [
                        'key'         => 'order_crypto_bch',
                        'title'       => 'Товар заказан (BCH)',
                        'description' => 'Выводит информацию о новом заказе (BCH)',
                        'tab'         => 'Bitcoin Cash (BCH)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Переведите на адрес <b>Bitcoin Cash</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>

                           ',
                    ],
                    [
                        'key'         => 'order_crypto_ltc',
                        'title'       => 'Товар заказан (LTC)',
                        'description' => 'Выводит информацию о новом заказе (LTC)',
                        'tab'         => 'Litecoin (LTC)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Переведите на адрес <b>Litecoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>

                           ',
                    ],
                    [
                        'key'         => 'order_crypto_eth',
                        'title'       => 'Товар заказан (ETH)',
                        'description' => 'Выводит информацию о новом заказе (ETH)',
                        'tab'         => 'Ethereum (ETH)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Переведите на адрес <b>Ethereum</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>

                          ',
                    ],

                    [
                        'key'         => 'order_kuna',
                        'title'       => 'Товар заказан (KUNA)',
                        'description' => 'Выводит информацию о новом заказе (KUNA)',
                        'tab'         => 'KUNA',
                        'content'     => '💰 <b>Вы заказали</b>
                        {order-product-product_type-name} на сумму {order-price}
                        в районе <b>{order-product-location}</b>.
                        До конца резерва осталось {order-reserve-left}.
                        Номер заказа: {order-number}.
                        ➖➖➖➖➖➖➖➖➖

                        Оплатите заказ с помощью KUNA-кода.
                        KUNA-код - это своеобразный ваучер, который можно использовать для оплаты заказа. Kuna код состоит из 45 символов (или 9 сегментов по 5 символов) и суфикса. Мы принимаем только гривенные коды, которые выглядят примерно вот так:
                        857ny-XXXXX-XXXXX-XXXXX-XXXXX-XXXXX-XXXXX-XXXXX-XXXXX-KUN-KCode
                        Получить KUNA-код можно на https://kuna.io.
                        Внимание! Если KUNA-код будет на сумму больше, чем стоимость товара, то вы получите адрес, но не получите сдачу! KUNA-код обязательно должен быть в валюте UAH!

                        ➖➖➖➖➖➖➖➖➖

                        Введите KUNA-код
                        на сумму {order-amount-unpaid}

                       ',
                    ],

                    [
                        'key'         => 'order_global_money_card',
                        'title'       => 'Товар заказан (Global Money - Visa/Mastercard)',
                        'description' => 'Выводит информацию о новом заказе (Global Money - Visa/Mastercard)',
                        'tab'         => 'Global Money (Card)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью карты Visa/Mastercard.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже, отправьте боту сюда.
                            https://telegra.ph/ID-operacii-09-03

                            ',
                    ],

                    [
                        'key'         => 'order_global_money_online',
                        'title'       => 'Товар заказан (Global Money - online)',
                        'description' => 'Выводит информацию о новом заказе (Global Money - online)',
                        'tab'         => 'Global Money (Online)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью перевода между кошельками Global Money.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже, отправьте боту сюда.
                            https://telegra.ph/ID-operacii-09-09

                          ',
                    ],

                    [
                        'key'         => 'order_global_money_terminal',
                        'title'       => 'Товар заказан (Global Money - Terminal)',
                        'description' => 'Выводит информацию о новом заказе (Global Money - Terminal)',
                        'tab'         => 'Global Money (Terminal)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью терминала
                            Переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                            Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                            Пример на фото ниже, отправьте код боту сюда
                            https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21

                           ',
                    ],

                    [
                        'key'         => 'order_easy_pay_terminal',
                        'title'       => 'Товар заказан (EasyPay - Terminal)',
                        'description' => 'Выводит информацию о новом заказе (EasyPay - Terminal)',
                        'tab'         => 'EasyPay (Terminal)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью терминала
                            Переведите сумму {order-amount-unpaid}
                            на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации должен состоять из 9-10 цифр.
                            Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                            Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                            Пример на фото ниже, отправьте код боту сюда
                            https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png

                           ',
                    ],

                    [
                        'key'         => 'order_easy_pay_online',
                        'title'       => 'Товар заказан (EasyPay - online)',
                        'description' => 'Выводит информацию о новом заказе (EasyPay - online)',
                        'tab'         => 'EasyPay (Online)',
                        'content'     => '💰 <b>Вы заказали</b>
                            {order-product-product_type-name} на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.
                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью перевода между кошельками EasyPay.
                            Переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                            на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖
                            Код активации должен состоять из 9-10 цифр как на фото-примере выше

                            После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда. Пример, как найти "ID ОПЕРАЦИИ", ниже.
                            https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13

                           ',
                    ],

                    [
                        'key'         => 'order_pay_methods',
                        'title'       => 'Вариант, когда можно выбрать способ оплаты',
                        'description' => null,
                        'content'     => '❗️ Выберите способ оплаты:
                           ',
                    ],
                    [
                        'key'         => 'order_pay_method',
                        'title'       => 'Шаблон каждого способа оплаты',
                        'description' => null,
                        'content'     => '💰 <i>{item-payment-title}</i> 👉 /order_{product_type-number}_{location-number}_{item-payment}',
                    ],
                    [
                        'key'         => 'order_pay_method_divider',
                        'title'       => 'Разделитель между способами оплаты',
                        'description' => null,
                        'content'     => "\n",
                    ],
                    [
                        'key'         => 'order_product_location_absent',
                        'title'       => 'Вариант, когда в выбранной локации отсутствует доступный для резерва запрошенный тип товара',
                        'description' => 'По умолчанию будет выдан вариант ошибки с кодом 5',
                        'content'     => '❗️ <b>К сожалению,</b>
                            {product_type-name} в районе {location-name} закончился.

                            Другой товар
                            <i>Жми</i> 👉 /products

                            Другой район
                            <i>Жми</i> 👉 /locations

                           ',
                    ],
                    [
                        'key'         => 'order_pay_method_absent',
                        'title'       => 'Вариант, когда нет доступных способов оплаты',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            в данный момент мы не можем принять оплату.
                            Попробуйте немного позже.

                            ',
                    ],
                    [
                        'key'         => 'order_wallet_busy',
                        'title'       => 'Вариант, когда заняты все кошельки вида "Один заказ - один кошелек"',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            в данный момент мы не можем принять оплату данным способом.
                            Попробуйте немного позже, либо попробуйте выбрать другой способ оплаты.

                            ',
                    ],
                    [
                        'key'         => 'order_error',
                        'title'       => 'Ошибка создания заказа',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            при заказе товара произошла ошибка. Вернитесь в главное меню и попробуйте заказать этот товар заново! Если не помогло, то обратитесь к оператору и сообщите, что при заказе у вас возникает ошибка с кодом {error-code}.

                            ',
                    ],
                    [
                        'key'         => 'order_select_location',
                        'title'       => 'Вариант, когда при заказе необходимо уточнить дочерние локации',
                        'description' => null,
                        'content'     => '<b>{product_type-name}</b>
                            <b>{location-name}</b>

                            ❗️ Для продолжения закaза
                            уточните район:

                            {list}

                           ',
                    ],
                    [
                        'key'         => 'order_location_product_absent',
                        'title'       => 'Вариант, когда в дочерних локациях нет указанного товара',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            {product_type-name} в районе {location-name} закончился.

                            Выбрать товар
                            <i>Жми</i> 👉 /products

                            Выбрать район
                            <i>Жми</i> 👉 /locations

                            ',
                    ],
                    [
                        'key'         => 'order_location_child',
                        'title'       => 'Шаблон каждой дочерней локации',
                        'description' => null,
                        'content'     => '🚩 <i>{item-location-name}</i>{item-location-discount}
                            <i>Выбрать</i> 👉 /order_{product_type-number}_{item-location-number}',
                    ],
                    [
                        'key'         => 'order_location_divider',
                        'title'       => 'Разделитель между дочерними локациями',
                        'description' => null,
                        'content'     => "\n- - - - - - - - - - - - - - - -\n",
                    ],
                    [
                        'key'         => 'order_discount',
                        'title'       => 'Шаблон информации о скидке',
                        'description' => null,
                        'content'     => "\n<b>+ скидка до {percent}%</b>",
                    ],
                ],
            ],

            [
                'keys'      => ['/last_order', '4'],
                'title'     => 'Последний заказ клиента',
                'templates' => [
                    [
                        'key'         => 'last_order',
                        'title'       => 'Информация о последнем заказе клиента',
                        'description' => 'Выводит информацию о последнем полученном заказе в случае если заказ был оплачен или отдан оператором (включая и «Переклад»).',
                        'content'     => '💰 <b>Ваш заказ</b>
                            Оплачен на сумму {order-amount-paid}
                            ➖➖➖➖➖➖➖➖➖

                            Ваш заказ #{order_number}
                            Оформлен {order_date}
                            ➖➖➖➖➖➖➖➖➖
                            {order-product-coordinates}
                            {order-product-content}
                            ',
                    ],
                    [
                        'key'         => 'last_order_absent',
                        'title'       => 'Вариант, когда нет информации о последнем заказе',
                        'description' => null,
                        'content'     => '❗️ <b>К сожалению,</b>
                            у нас нет информации о Вашем последнем заказе.

                           ',
                    ],
                ],
            ],

            [
                'keys'      => ['/order_check', '5'],
                'title'     => 'Информация о заказе',
                'templates' => [
                    [
                        'key'         => 'order_wait_payment_qiwi_3',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (QIWI, ручной кошелек)',
                        'description' => null,
                        'tab'         => 'QIWI 3',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Переведите на QIWI-кошелек
                        // <b>{order-purse-phone}</b>
                        // сумму <b>{order-amount-unpaid}</b>.
                        // В комментарии к платежу ничего указывать не нужно.
                        // <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                        // <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.

                    ],
                    [
                        'key'         => 'order_pay_partially_qiwi_3',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (QIWI, ручной кошелек)',
                        'description' => null,
                        'tab'         => 'QIWI 3',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на QIWI-кошелек
                        // <b>{order-purse-phone}</b>
                        // сумму <b>{order-amount-unpaid}</b>.
                        // В комментарии к платежу ничего указывать не нужно.
                        // <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                        // <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],
                    [
                        'key'         => 'order_wait_payment_crypto_btc',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (BTC)',
                        'description' => null,
                        'tab'         => 'Bitcoin (BTC)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Bitcoin</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_crypto_btc',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (BTC)',
                        'description' => null,
                        'tab'         => 'Bitcoin (BTC)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-crypto-amount-paid} или {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Bitcoin</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_crypto_bch',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (BCH)',
                        'description' => null,
                        'tab'         => 'Bitcoin Cash (BCH)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Bitcoin Cash</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_crypto_bch',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (BCH)',
                        'description' => null,
                        'tab'         => 'Bitcoin Cash (BCH)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-crypto-amount-paid} или {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Bitcoin Cash</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_crypto_ltc',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (LTC)',
                        'description' => null,
                        'tab'         => 'Litecoin (LTC)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Litecoin</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_crypto_ltc',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (LTC)',
                        'description' => null,
                        'tab'         => 'Litecoin (LTC)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-crypto-amount-paid} или {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Litecoin</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_crypto_eth',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (ETH)',
                        'description' => null,
                        'tab'         => 'Ethereum (ETH)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        //     ➖➖➖➖➖➖➖➖➖

                        //     Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                        //     Переведите на адрес <b>Ethereum</b>:
                        //     <b>{order-crypto-address}</b>
                        //     сумму <b>{order-crypto-amount-unpaid}</b>

                        //    ',
                    ],
                    [
                        'key'         => 'order_pay_partially_crypto_eth',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (ETH)',
                        'description' => null,
                        'tab'         => 'Ethereum (ETH)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-crypto-amount-paid} или {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // Переведите на адрес <b>Ethereum</b>:
                        // <b>{order-crypto-address}</b>
                        // сумму <b>{order-crypto-amount-unpaid}</b>

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_kuna',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (KUNA)',
                        'description' => null,
                        'tab'         => 'KUNA',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите KUNA-код</b>
                        // на сумму <b>{order-amount-unpaid}</b>

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_kuna',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (KUNA)',
                        'description' => null,
                        'tab'         => 'KUNA',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.
                        // <b>Введите KUNA-код</b>
                        // на сумму <b>{order-amount-unpaid}</b>

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_global_money_card',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (Global Money - Visa/Mastercard)',
                        'description' => null,
                        'tab'         => 'Global Money Card',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Оплатите заказ с помощью карты Visa/Mastercard.
                        // На странице https://global24.ua/replenishbycard
                        // переведите сумму {order-amount-unpaid}
                        // на Global Money кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                        // https://telegra.ph/ID-operacii-09-03

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_global_money_card',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (Global Money - Visa/Mastercard)',
                        'description' => null,
                        'tab'         => 'Global Money Card',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.

                        // Доплатите с помощью карты Visa/Mastercard.
                        // На странице https://global24.ua/replenishbycard
                        // переведите сумму {order-amount-unpaid}
                        // на Global Money кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                        // https://telegra.ph/ID-operacii-09-03

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_global_money_online',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (Global Money - online)',
                        'description' => null,
                        'tab'         => 'Global Money (Online)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Оплатите заказ с помощью перевода между кошельками Global Money.
                        // На странице https://global24.ua/replenishbycard
                        // переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                        // на Global Money кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                        // https://telegra.ph/ID-operacii-09-09

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_global_money_online',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (Global Money - online)',
                        'description' => null,
                        'tab'         => 'Global Money (online)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.

                        // Доплатите с помощью перевода между кошельками Global Money.
                        // На странице https://global24.ua/replenishbycard
                        // переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                        // на Global Money кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                        // https://telegra.ph/ID-operacii-09-09

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_global_money_terminal',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (Global Money - terminal)',
                        'description' => null,
                        'tab'         => 'Global Money (Terminal)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        //     ➖➖➖➖➖➖➖➖➖

                        //     Оплатите заказ с помощью терминала
                        //     Переведите сумму {order-amount-unpaid}
                        //     на Global Money кошелек {wallet-number}

                        //     ➖➖➖➖➖➖➖➖➖

                        //     Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                        //     Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                        //     Пример на фото ниже, отправьте код боту сюда
                        //     https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21

                        //    ',
                    ],
                    [
                        'key'         => 'order_pay_partially_global_money_terminal',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (Global Money - terminal)',
                        'description' => null,
                        'tab'         => 'Global Money (Terminal)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.

                        // Доплатите заказ с помощью терминала
                        // Переведите сумму {order-amount-unpaid}
                        // на Global Money кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                        // Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                        // Пример на фото ниже, отправьте код боту сюда
                        // https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_easy_pay_online',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (EasyPay - online)',
                        'description' => null,
                        'tab'         => 'EasyPay (Online)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Оплатите заказ с помощью перевода между кошельками EasyPay.
                        // переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                        // на EasyPay кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Код активации должен состоять из 9-10 цифр как на фото-примере выше
                        // После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда. Пример, как найти "ID ОПЕРАЦИИ", ниже.
                        // https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_easy_pay_online',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (EasyPay - online)',
                        'description' => null,
                        'tab'         => 'EasyPay (online)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.

                        // Доплатите с помощью перевода между кошельками EasyPay.
                        // переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                        // на EasyPay кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // <b>Введите код активации.</b>
                        // Код активации должен состоять из 9-10 цифр как на фото-примере выше
                        // После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда. Пример, как найти "ID ОПЕРАЦИИ", ниже.
                        // https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],

                    [
                        'key'         => 'order_wait_payment_easy_pay_terminal',
                        'title'       => 'Информация о заказе в случае если заказ "Ожидает оплаты" (EasyPay - terminal)',
                        'description' => null,
                        'tab'         => 'EasyPay (Terminal)',
                        'content'     => '❗️ <b>Ваш заказ не оплачен!</b>
                            {order-product-product_type-name} ({order-product-location}).
                            До конца резерва осталось {order-reserve-left}.
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Оплатите заказ с помощью терминала
                        // Переведите сумму {order-amount-unpaid}
                        // на EasyPay кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // Код активации должен состоять из 9-10 цифр.
                        // Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                        // Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                        // Пример на фото ниже, отправьте код боту сюда
                        // https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png

                        // ',
                    ],
                    [
                        'key'         => 'order_pay_partially_easy_pay_terminal',
                        'title'       => 'Информация о заказе в случае если заказ "Частично оплачен" (EasyPay - terminal)',
                        'description' => null,
                        'tab'         => 'EasyPay (Terminal)',
                        'content'     => '❗️ <b>Ваш заказ частично оплачен!</b>
                            на сумму {order-amount-paid}
                            {order-product-product_type-name} ({order-product-location}).
                            Номер заказа: {order-number}.',
                        // ➖➖➖➖➖➖➖➖➖

                        // Вам осталось доплатить <b>{order-amount-unpaid}</b>.

                        // Оплатите заказ с помощью терминала
                        // Переведите сумму {order-amount-unpaid}
                        // на EasyPay кошелек {wallet-number}

                        // ➖➖➖➖➖➖➖➖➖

                        // Код активации должен состоять из 9-10 цифр.
                        // Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                        // Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                        // Пример на фото ниже, отправьте код боту сюда
                        // https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png

                        // ➖➖➖➖➖➖➖➖➖
                        // ✔️ Проверить еще раз
                        // <i>Жми</i> 👉 /order_check',
                    ],
                ],
            ],

            [
                'keys'      => ['/order_cancel', '6'],
                'title'     => 'Отмена заказа',
                'templates' => [
                    [
                        'key'         => 'order_cancel_success',
                        'title'       => 'Заказ успешно отменен',
                        'description' => 'Сообщение выводится при успешной отмене заказа с помощью комманды /order_cancel. Клиент может отменить заказ только если он находится в статусе «Ожидает оплаты».',
                        'content'     => '❗️ <b>Ваш заказ отменен!</b>

                            ',
                    ],
                    [
                        'key'         => 'order_cancel_error',
                        'title'       => 'Ошибка при отмене заказа',
                        'description' => 'Данное сообщение будет выведено при ошибке отмены, если такое случится.',
                        'content'     => '❗️ <b>Ошибка!</b>
                            При отмене заказа возникла ошибка.

                            Попробуйте еще раз
                           ',
                    ],
                    [
                        'key'         => 'order_cancel_denied',
                        'title'       => 'Отказано в отмене заказа',
                        'description' => 'Данное сообщение будет выведено, если клиенту запрещено отменять свой заказ. Например в случае если заказ уже частично оплачен. В таком случае оператор сам решает отменять заказ или нет.',
                        'content'     => '❗️ <b>Невозможно отменить заказ!</b>
                            Вы не можете отменить свой заказ.
                            Обратитесь к оператору.

                           ',
                    ],
                ],
            ],

        ],

        'events' => [
            [
                'key'         => 'order_partially_paid_qiwi_3',
                'title'       => 'Уведомление о частичной оплате заказа (QIWI, ручной кошелек)',
                'description' => 'Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (QIWI, ручной кошелек)',
                'tab'         => 'QIWI 3',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Переведите на QIWI-кошелек
                    <b>{order-purse-phone}</b>
                    сумму <b>{order-amount-unpaid}</b>.
                    В комментарии к платежу ничего указывать не нужно.
                    <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                    <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_crypto_btc',
                'title'       => 'Уведомление о частичной оплате заказа (BTC)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (BTC)',
                'tab'         => 'Bitcoin (BTC)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-crypto-amount-paid} или {order-amount-paid}
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Переведите на адрес <b>Bitcoin</b>:
                    <b>{order-crypto-address}</b>
                    сумму <b>{order-crypto-amount-unpaid}</b>

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_crypto_bch',
                'title'       => 'Уведомление о частичной оплате заказа (BCH)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (BCH)',
                'tab'         => 'Bitcoin Cash (BCH)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-crypto-amount-paid} или {order-amount-paid}
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Переведите на адрес <b>Bitcoin Cash</b>:
                    <b>{order-crypto-address}</b>
                    сумму <b>{order-crypto-amount-unpaid}</b>

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_crypto_ltc',
                'title'       => 'Уведомление о частичной оплате заказа (LTC)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (LTC)',
                'tab'         => 'Litecoin (LTC)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-crypto-amount-paid} или {order-amount-paid}
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Переведите на адрес <b>Litecoin</b>:
                    <b>{order-crypto-address}</b>
                    сумму <b>{order-crypto-amount-unpaid}</b>

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_crypto_eth',
                'title'       => 'Уведомление о частичной оплате заказа (ETH)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (ETH)',
                'tab'         => 'Ethereum (ETH)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-crypto-amount-paid} или {order-amount-paid}
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Переведите на адрес <b>Ethereum</b>:
                    <b>{order-crypto-address}</b>
                    сумму <b>{order-crypto-amount-unpaid}</b>

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_kuna',
                'title'       => 'Уведомление о частичной оплате заказа (KUNA)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (KUNA)',
                'tab'         => 'KUNA',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    <b>Введите KUNA-код</b>
                    на сумму <b>{order-amount-unpaid}</b>

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_global_money_card',
                'title'       => 'Уведомление о частичной оплате заказа (Global Money - Visa/Mastercard)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (Global Money - Visa/Mastercard)',
                'tab'         => 'Global Money Card',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Доплатите с помощью карты Visa/Mastercard.
                    На странице https://global24.ua/replenishbycard
                    переведите сумму {order-amount-unpaid}
                    на Global Money кошелек {wallet-number}

                    ➖➖➖➖➖➖➖➖➖

                    <b>Введите код активации.</b>
                    Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                    https://telegra.ph/ID-operacii-09-03

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_global_money_online',
                'title'       => 'Уведомление о частичной оплате заказа (Global Money - online)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (Global Money - online)',
                'tab'         => 'Global Money (online)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Доплатите с помощью перевода между кошельками Global Money.
                    На странице https://global24.ua/replenishbycard
                    переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                    на Global Money кошелек {wallet-number}

                    ➖➖➖➖➖➖➖➖➖

                    <b>Введите код активации.</b>
                    Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                    https://telegra.ph/ID-operacii-09-09

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_global_money_terminal',
                'title'       => 'Уведомление о частичной оплате заказа (Global Money - terminal)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (Global Money - terminal)',
                'tab'         => 'Global Money (Terminal)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Доплатите заказ с помощью терминала
                    Переведите сумму {order-amount-unpaid}
                    на Global Money кошелек {wallet-number}

                    ➖➖➖➖➖➖➖➖➖

                    Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                    Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                    Пример на фото ниже, отправьте код боту сюда
                    https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_easy_pay_online',
                'title'       => 'Уведомление о частичной оплате заказа (EasyPay - online)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (EasyPay - online)',
                'tab'         => 'EasyPay (online)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Оплатите заказ с помощью перевода между кошельками EasyPay.
                    Переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                    на EasyPay кошелек {wallet-number}

                    ➖➖➖➖➖➖➖➖➖

                    <b>Введите код активации.</b>
                    Код активации должен состоять из 9-10 цифр как на фото-примере выше
                    После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда.
                    Пример, как найти "ID ОПЕРАЦИИ", ниже.
                    https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_partially_paid_easy_pay_terminal',
                'title'       => 'Уведомление о частичной оплате заказа (EasyPay - terminal)',
                'description' => ' Уведомление отправляется клиенту если он оплатил сумму меньше стоимости товара (Global Money - terminal)',
                'tab'         => 'EasyPay (Terminal)',
                'content'     => '❗️ <b>Ваш заказ #{order-number}</b>
                    оплачен на сумму {order-amount-paid}.
                    Вам осталось доплатить еще {order-amount-unpaid}.
                    ➖➖➖➖➖➖➖➖➖

                    Оплатите заказ с помощью терминала
                    Переведите сумму {order-amount-unpaid}
                    на EasyPay кошелек {wallet-number}

                    ➖➖➖➖➖➖➖➖➖

                    Код активации должен состоять из 9-10 цифр.
                    Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                    Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                    Пример на фото ниже, отправьте код боту сюда
                    https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png

                    ➖➖➖➖➖➖➖➖➖
                    ✔️ Проверить заказ
                    <i>Жми</i> 👉 /order_check',
            ],

            [
                'key'         => 'order_paid',
                'title'       => 'Уведомление об оплате заказа',
                'description' => 'Уведомление отправляется клиенту если он оплатил сумму равную либо больше чем стоимость товара',
                'content'     => '💰 <b>Ваш заказ оплачен!</b>
                    Заберите товар по координатам:
                    ➖➖➖➖➖➖➖➖➖

                    {order-product-coordinates}
                    {order-product-content}
                    ➖➖➖➖➖➖➖➖➖
                    Благодарим за покупку!
                    ',
            ],

            [
                'key'         => 'order_cancel_by_timeout',
                'title'       => 'Уведомление об отмене заказа по таймауту',
                'description' => 'Уведомление отправляется клиенту системой мониторинга при отмене заказа по таймауту. Данное событие наступает только после успешной проверки транзакций системой мониторинга если по заказу не поступила оплата и вышло время резерва товара.
                    * Автоматически отменяться могут только заказы, по которым не зафиксировано ни одной транзакции (статус «Ожидает оплаты»). Заказы со статусом «Частично оплачен» не отменяются по таймауту.',
                'content' => '❗️ <b>Оплата не поступила</b>
                    Заказ отменен!

                   ',
            ],
        ],

        'operator_notifications' => [
            [
                'key'         => 'give',
                'title'       => 'Уведомление при действии "Отдать"',
                'description' => 'Уведомление отправляется клиенту, когда оператор нажимает кнопку «Отдать» в меню заказа.',
                'content'     => '🎁 <b>Получите заказ!</b>
                    Заберите товар по координатам:
                    ➖➖➖➖➖➖➖➖➖

                    {order-product-coordinates}
                    {order-product-content}
                    ',
            ],
            [
                'key'         => 'relocation',
                'title'       => 'Уведомление при действии "Переклад"',
                'description' => 'Уведомление отправляется клиенту, когда оператор нажимает кнопку «Переклад» в меню заказа.',
                'content'     => '🎁 <b>Получите заказ!</b>
                    Заберите товар по координатам:
                    ➖➖➖➖➖➖➖➖➖

                    {order-product-coordinates}
                    {order-product-content}
                    ',
            ],
            [
                'key'         => 'cancel',
                'title'       => 'Уведомление при действии "Отмена"',
                'description' => 'Уведомление отправляется клиенту, когда оператор нажимает кнопку «Отмена» в меню заказа.',
                'content'     => '❗️ Оператор отменил Ваш заказ

                    ',
            ],
        ],

        'antispam' => [
            [
                'key'     => 'frequent_requests',
                'title'   => 'Бан клиентов за частые запросы без заказа',
                'options' => [
                    [
                        'key'         => 'limit',
                        'title'       => 'Лимит обращений до заказа',
                        'description' => 'Количество запросов от клиента, при достижении которого он будет забанен (в случае если он не совершает заказ, а просто дергает бота).
                            * 0 - не ограничено (антиспам выключен).',
                        'value' => 60,
                    ],
                    [
                        'key'         => 'send_notice',
                        'title'       => 'Отправлять предупреждение о возможном бане за частые запросы',
                        'description' => 'Укажите на каких запросах (по счету) отправлять предупреждение о возможном бане. Например, "10" означает, что предупреждение будет выслано при 10-ом запросе. "10,20,50" - на 10-ом, 20-ом и 50-ом запросе.
                            * Пусто - предупреждения высылаться не будут.',
                        'value' => '20,40,55',
                    ],
                    [
                        'key'         => 'notice_text',
                        'title'       => 'Предупреждение о возможном бане за частые запросы',
                        'description' => null,
                        'value'       => '❗️ <b>Предупреждение!</b>
                            Не нужно просто так дергать бота. Сделайте заказ, иначе будете забанены!',
                    ],
                    [
                        'key'         => 'ban_text',
                        'title'       => 'Уведомление о бане за частые запросы',
                        'description' => null,
                        'value'       => '❗️ <b>Вы забанены!</b>
                            на {ban-duration} минут',
                    ],
                    [
                        'key'         => 'ban_duration',
                        'title'       => 'Продолжительность бана, минут',
                        'description' => 'Время, на которое клиент будет забанен.',
                        'value'       => 15,
                    ],
                ],
            ],

            [
                'key'     => 'frequent_orders_without_payment',
                'title'   => 'Блокировка за частые заказы без оплаты',
                'options' => [
                    [
                        'key'         => 'limit',
                        'title'       => 'Лимит подряд идущих неоплаченных заказов',
                        'description' => 'Количество отмен заказов, при достижении которого клиент будет забанен (в случае если заказ отменяется автоматически либо клиентом. Отмены оператором не учитываются).
                            * 0 - не ограничено (антиспам выключен).',
                        'value' => 3,
                    ],
                    [
                        'key'         => 'send_notice',
                        'title'       => 'Отправлять предупреждение о возможном бане за частые неоплаченные заказы',
                        'description' => 'При включенном положении клиент будет автоматически получать предупреждение о возможном бане при каждой отмене заказа (кроме отмены оператором).',
                        'value'       => true,
                    ],
                    [
                        'key'         => 'notice_text',
                        'title'       => 'Предупреждение о возможном бане за частые неоплаченные заказы',
                        'description' => null,
                        'value'       => '❗️ <b>Предупреждение!</b>
                            Запрещено резервировать товар без оплаты более {antispam-limit-cancels} раз!
                            У вас осталось {client-left-cancels} попыток.',
                    ],
                    [
                        'key'         => 'ban_text',
                        'title'       => 'Уведомление о бане за частые неоплаченные заказы',
                        'description' => null,
                        'value'       => '❗️ <b>Вы забанены за заказы без оплаты!</b>
                            на {ban-duration} минут',
                    ],
                    [
                        'key'         => 'ban_duration',
                        'title'       => 'Продолжительность бана, минут',
                        'description' => null,
                        'value'       => 30,
                    ],
                ],
            ],

            [
                'key'     => 'wrong_payment_codes',
                'title'   => 'Блокировка за вводы неверных кодов оплаты',
                'options' => [
                    [
                        'key'         => 'limit',
                        'title'       => 'Лимит неверных вводов кода оплаты',
                        'description' => 'Количество вводов неверных кодов оплаты, при достижении которого клиент будет забанен
                            * 0 - не ограничено (антиспам выключен).',
                        'value' => 0,
                    ],
                    [
                        'key'         => 'send_notice',
                        'title'       => 'Отправлять предупреждение о возможном бане за вводы неверных кодов оплаты',
                        'description' => 'При включенном положении клиент будет автоматически получать предупреждение о возможном бане при каждом вводе неверного кода оплаты.',
                        'value'       => true,
                    ],
                    [
                        'key'         => 'notice_text',
                        'title'       => 'Предупреждение о возможном бане за вводы неверных кодов оплаты',
                        'description' => null,
                        'value'       => '❗️ <b>Предупреждение!</b>
                            У вас осталось {client-left-paycoupons} попыток.',
                    ],
                    [
                        'key'         => 'ban_text',
                        'title'       => 'Уведомление о бане за вводы неверных кодов оплаты',
                        'description' => null,
                        'value'       => '❗️ <b>Вы забанены</b>
                            на {ban-duration} минут',
                    ],
                    [
                        'key'         => 'ban_duration',
                        'title'       => 'Продолжительность бана, минут',
                        'description' => null,
                        'value'       => 30,
                    ],
                ],
            ],
        ],

        'reminders' => [
            [
                'key'     => 'payment',
                'title'   => 'Настройка напоминаний для заказов со статусом «Ожидает оплаты»',
                'options' => [
                    [
                        'key'         => 'intervals',
                        'title'       => 'Промежутки времени, через которые отправлять напоминание об оплате, минут',
                        'description' => ' Укажите, через какие промежутки времени (в минутах) отправлять напоминания для заказов со статусом «Ожидает оплаты». Стартовой точкой считается время создания заказа. Например "5" - означает, что напоминание будет отправлено через 5 минут после создания заказа. "10,5,3" - напоминание будет отправлено через 10 минут после создания заказа, затем еще через 5 минут, и еще через 3 минуты.
                            * Пусто - напоминания отправляться не будут.',
                        'value' => '5,10,10,30,60,60,120,120',
                    ],

                    [
                        'key'         => 'qiwi_3',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (QIWI, ручной кошелек)',
                        'description' => null,
                        'tab'         => 'QIWI 3',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на QIWI-кошелек
                            <b>{order-purse-phone}</b>
                            сумму <b>{order-amount-unpaid}</b>.
                            В комментарии к платежу ничего указывать не нужно.
                            <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                            <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.',
                    ],

                    [
                        'key'         => 'crypto_btc',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (BTC)',
                        'description' => null,
                        'tab'         => 'Bitcoin (BTC)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                            Переведите на адрес <b>Bitcoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>',
                    ],

                    [
                        'key'         => 'crypto_bch',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (BCH)',
                        'description' => null,
                        'tab'         => 'Bitcoin Cash (BCH)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                            Переведите на адрес <b>Bitcoin Cash</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>',
                    ],

                    [
                        'key'         => 'crypto_ltc',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (LTC)',
                        'description' => null,
                        'tab'         => 'Litecoin (LTC)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                            Переведите на адрес <b>Litecoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>',
                    ],

                    [
                        'key'         => 'crypto_eth',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (ETH)',
                        'description' => null,
                        'tab'         => 'Ethereum (ETH)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            Вам необходимо оплатить <b>{order-amount-unpaid}</b>.
                            Переведите на адрес <b>Ethereum</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>',
                    ],

                    [
                        'key'         => 'kuna',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (KUNA)',
                        'description' => null,
                        'tab'         => 'KUNA',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖
                            <b>Введите KUNA-код</b>
                            на сумму <b>{order-amount-unpaid}</b>',
                    ],

                    [
                        'key'         => 'global_money_card',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (Global Money - Visa/Mastercard)',
                        'description' => null,
                        'tab'         => 'Global Money Card',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью карты Visa/Mastercard.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                            Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                            https://telegra.ph/ID-operacii-09-03',
                    ],

                    [
                        'key'         => 'global_money_online',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (Global Money - online)',
                        'description' => null,
                        'tab'         => 'Global Money (online)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью перевода между кошельками Global Money.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                            Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                            https://telegra.ph/ID-operacii-09-09',
                    ],

                    [
                        'key'         => 'global_money_terminal',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (Global Money - terminal)',
                        'description' => null,
                        'tab'         => 'Global Money (Terminal)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью терминала
                            Переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                            Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                            Пример на фото ниже, отправьте код боту сюда
                            https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21',
                    ],

                    [
                        'key'         => 'easy_pay_online',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (Global Money - online)',
                        'description' => null,
                        'tab'         => 'Global Money (online)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖

                           Оплатите заказ с помощью перевода между кошельками EasyPay.
                           Переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                           на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                          Код активации должен состоять из 9-10 цифр как на фото-примере выше
                          После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда. Пример, как найти "ID ОПЕРАЦИИ", ниже.
                          https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13',
                    ],

                    [
                        'key'         => 'easy_pay_terminal',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Ожидает оплаты" (EasyPay - terminal)',
                        'description' => null,
                        'tab'         => 'EasyPay (Terminal)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b> на сумму {order-price}
                            в районе <b>{order-product-location}</b>.
                            Номер заказа: {order-number}.
                            До конца резерва осталось {order-reserve-left}.

                            ➖➖➖➖➖➖➖➖➖

                           Оплатите заказ с помощью терминала
                           Переведите сумму {order-amount-unpaid}
                           на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖
                           Код активации должен состоять из 9-10 цифр.
                           Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                           Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                           Пример на фото ниже, отправьте код боту сюда
                           https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png',
                    ],
                ],
            ],

            [
                'key'     => 'payment_partially',
                'title'   => 'Настройка напоминаний для заказов со статусом «Частично оплачен»',
                'options' => [
                    [
                        'key'   => 'intervals',
                        'title' => 'Укажите, через какие промежутки времени (в минутах) отправлять напоминания для заказов со статусом «Частично оплачен». Стартовой точкой считается время последней оплаты. Например "5" - означает, что напоминание будет отправлено через 5 минут после последней оплаты. "10,5,3" - напоминание будет отправлено через 10 минут после оплаты, затем еще через 5 минут, и еще через 3 минуты.
                            * Пусто - напоминания отправляться не будут.',
                        'value' => '5,10,10,30,60,60,120,120',
                    ],

                    [
                        'key'         => 'qiwi_3',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (QIWI, ручной кошелек)',
                        'description' => null,
                        'tab'         => 'QIWI 3',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на QIWI-кошелек
                            <b>{order-purse-phone}</b>
                            сумму <b>{order-amount-unpaid}</b>.
                            В комментарии к платежу ничего указывать не нужно.
                            <b>Внимание!</b> После оплаты сообщите оператору номер заказа <b>{order-number}</b>, номер кошелька <b>{order-purse-phone}</b>, оплаченную сумму, дату и время платежа, а также номер кошелька, с которого Вы совершили оплату (если не терминал).
                            <b>Внимание!</b> Сообщать об оплате нужно именно оператору, а не боту! Однако адрес выдаст Вам бот.
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'crypto_btc',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (BTC)',
                        'description' => null,
                        'tab'         => 'Bitcoin (BTC)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на адрес <b>Bitcoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'crypto_bch',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (BCH)',
                        'description' => null,
                        'tab'         => 'Bitcoin Cash (BCH)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на адрес <b>Bitcoin Cash</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'crypto_ltc',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (LTC)',
                        'description' => null,
                        'tab'         => 'Litecoin (LTC)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на адрес <b>Litecoin</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'crypto_eth',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (ETH)',
                        'description' => null,
                        'tab'         => 'Ethereum (ETH)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            Переведите на адрес <b>Ethereum</b>:
                            <b>{order-crypto-address}</b>
                            сумму <b>{order-crypto-amount-unpaid}</b>
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'kuna',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (KUNA)',
                        'description' => null,
                        'tab'         => 'KUNA',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖
                            <b>Введите KUNA-код</b>
                            на сумму <b>{order-amount-unpaid}</b>.
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'global_money_card',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (Global Money - Visa/Mastercard)',
                        'description' => null,
                        'tab'         => 'Global Money Card',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖

                            Доплатите с помощью карты Visa/Mastercard.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                            Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                            https://telegra.ph/ID-operacii-09-03
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'global_money_online',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (Global Money - online)',
                        'description' => null,
                        'tab'         => 'Global Money (online)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖

                            Доплатите с помощью перевода между кошельками Global Money.
                            На странице https://global24.ua/replenishbycard
                            переведите сумму {order-amount-unpaid} со своего Global Money кошелька
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                            Он находится в самом низу квитанции о переводе (поле "ID операции"), как на фото ниже.
                            https://telegra.ph/ID-operacii-09-09
                            Если Вы в ближайшее время полностью не оплатите заказ, то он может быть отменен без возврата средств!',
                    ],

                    [
                        'key'         => 'global_money_terminal',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (Global Money - terminal)',
                        'description' => null,
                        'tab'         => 'Global Money (Terminal)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖

                            Доплатите заказ с помощью терминала
                            Переведите сумму {order-amount-unpaid}
                            на Global Money кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            Код активации это комбинация из ТОЧНОЙ суммы пополнения и времени (часы и минуты) в чеке.
                            Например, сумма пополнения 45 грн, а время в чеке 12:30, код будет 451230.
                            Пример на фото ниже, отправьте код боту сюда
                            https://telegra.ph/B7Fsd9qwRxdNYqmCpma7ZGReG-02-21',
                    ],

                    [
                        'key'         => 'easy_pay_online',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (EasyPay - online)',
                        'description' => null,
                        'tab'         => 'EasyPay (online)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖

                            Оплатите заказ с помощью перевода между кошельками EasyPay.
                            Переведите сумму {order-amount-unpaid} со своего EasyPay кошелька
                            на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                            <b>Введите код активации.</b>
                            Код активации должен состоять из 9-10 цифр как на фото-примере выше
                            После пополнения, в самом низу квитанции о переводе, найдите "ID ОПЕРАЦИИ", скопируйте его и отправьте боту сюда. Пример, как найти "ID ОПЕРАЦИИ", ниже.
                            https://telegra.ph/MhSY9zyaqkMLLk3c4Nji-02-13',
                    ],

                    [
                        'key'         => 'easy_pay_terminal',
                        'title'       => 'Напоминание об оплате для заказов со статусом "Частично оплачен" (EasyPay - terminal)',
                        'description' => null,
                        'tab'         => 'EasyPay (Terminal)',
                        'value'       => '❗️ <b>Напоминаем</b>,
                            что за Вами зарезервирован
                            <b>{order-product-product_type-name}</b>.
                            Вы частично оплатили заказ на сумму {order-amount-paid}.
                            Вам осталось доплатить еще {order-amount-unpaid}.
                            Номер заказа: {order-number}.

                            ➖➖➖➖➖➖➖➖➖

                            Доплатите заказ с помощью терминала
                            Переведите сумму {order-amount-unpaid}
                            на EasyPay кошелек {wallet-number}

                            ➖➖➖➖➖➖➖➖➖

                           Код активации должен состоять из 9-10 цифр.
                           Код активации это комбинация из номера терминала и времени (часы и минуты) в чеке.
                           Например, номер терминала 72125, а время в чеке 20:41, код будет 721252041.
                           Пример на фото ниже, отправьте код боту сюда
                           https://telegra.ph/file/6f21b5b6aa91d6bc077d2.png',
                    ],
                ],

            ],
        ],

        'distribution' => [
            [
                'key'         => 'template_all_products',
                'title'       => 'Шаблон рассылки с информацией обо всех имеющихся товарах',
                'description' => 'Данный шаблон генерируется и ставится в очередь на рассылку клиентам бота при ручной генерации рассылки оператором. Текст рассылки должен содержать информацию обо всех имеющихся товарах по всем локациям. {list} - перечень локаций.',
                'content'     => '👍 <b>Товары в наличии</b>

                    {list}

                    ➖➖➖➖➖➖➖➖➖➖➖
                    Ⓜ️ <i>Меню</i> 👉 /menu',
            ],
            [
                'key'         => 'location_template',
                'title'       => 'Подшаблон каждой локации',
                'description' => 'Субшаблон единичной локации, содержащий информацию об имеющихся товарах в локации. {list} - перечень товаров в локации.',
                'content'     => '<b>{location-caption}</b>:

                    {list}',
            ],
            [
                'key'         => 'location_divider',
                'title'       => 'Разделитель между локациями',
                'description' => null,
                'content'     => "\n",
            ],
            [
                'key'         => 'max_depth_locations',
                'title'       => 'Max. уровень вложенности локаций',
                'description' => '* Max. уровень вложенности локаций, для которых будут генерироваться рассылки
* 0 - только конечные локации.',
                'content' => '0',
            ],
            [
                'key'         => 'location_product_template',
                'title'       => 'Подшаблон каждого типа товара в локации',
                'description' => 'Субшаблон единичного товара в локации, содержащий информацию о названии и цене.',
                'content'     => '📦 {item-product_type-name} - {item-product_type-price}
                    <i>Заказать</i> 👉 /order_{item-product_type-number}_{location-number}',
            ],
            [
                'key'         => 'location_product_divider',
                'title'       => 'Разделитель между типами товаров в локации',
                'description' => null,
                'content'     => "\n",
            ],
            [
                'key'         => 'new_products_template',
                'title'       => 'Шаблон рассылки с информацией о пополнении товарами',
                'description' => 'Данный текст генерируется и ставится в очередь на рассылку клиентам бота автоматически при добавлении (или импортировании) товаров оператором. Таким образом клиенты информируются о пополнении товарами.',
                'content'     => '➕ <b>Пополнение товарами</b>

                    🌆 <b>{location-caption}</b>
                    📦 {product_type-name} - {product_type-price}
                    <i>Заказать</i> 👉 /order_{product_type-number}_{location-number}

                    ➖➖➖➖➖➖➖➖➖➖➖
                    Ⓜ️ <i>Меню</i> 👉 /menu',
            ],
        ],
    ],
];

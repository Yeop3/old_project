<?php

declare(strict_types=1);

namespace App\Bot;

use App\Bot\Handlers\ConfirmTaxiOrderHandler;
use App\Bot\Handlers\ConfirmTaxiOrdersListHandler;
use App\Bot\Handlers\CreateProductHandler;
use App\Bot\Handlers\DriverHotOrdersListHandler;
use App\Bot\Handlers\DriverProcessHotOrderHandler;
use App\Bot\Handlers\DriverProcessTaxiOrderHandler;
use App\Bot\Handlers\DriverProductListHandler;
use App\Bot\Handlers\DriverTaxiOrdersListHandler;
use App\Bot\Handlers\LocationHandler;
use App\Bot\Handlers\LocationsHandler;
use App\Bot\Handlers\MenuHandler;
use App\Bot\Handlers\OrderCancelHandler;
use App\Bot\Handlers\OrderCheckHandler;
use App\Bot\Handlers\OrderHandler;
use App\Bot\Handlers\OrderHotHandler;
use App\Bot\Handlers\OrderLastHandler;
use App\Bot\Handlers\OrderSuccessHandle;
use App\Bot\Handlers\OrderTaxiHandler;
use App\Bot\Handlers\ProductHandler;
use App\Bot\Handlers\CleanCacheHandler;
use App\Bot\Handlers\ProductsHandler;

/**
 * Class HandlersMapper.
 */
final class HandlersMapper {

    public const MAP = [
        '/menu'        => MenuHandler::class,
        '0'            => MenuHandler::class,
        'ANYKEY'       => MenuHandler::class,
        'Главное меню' => MenuHandler::class,

        '/products'     => ProductsHandler::class,
        '1'             => ProductsHandler::class,
        'Товары и цены' => ProductsHandler::class,

        '/locations'    => LocationsHandler::class,
        '2'             => LocationsHandler::class,
        'Выбрать город' => LocationsHandler::class,

        '/location_([0-9]+)' => LocationHandler::class,
        '2_([0-9]+)'         => LocationHandler::class,

        '/product_([0-9]+)' => ProductHandler::class,
        '1_([0-9]+)'        => ProductHandler::class,

        '/order_([0-9]+)_([0-9]+)'          => OrderHandler::class,
        '30_([0-9]+)_([0-9]+)'              => OrderHandler::class,
        '/order_([0-9]+)_([0-9]+)_([0-9]+)' => OrderHandler::class,
        '31_([0-9]+)_([0-9]+)_([0-9]+)'     => OrderHandler::class,

        '/order_taxi_([0-9]+)_([0-9]+)' => OrderTaxiHandler::class,

        '/order_hot_([0-9]+)_([0-9]+)' => OrderHotHandler::class,

        '/my_delivery_orders'              => DriverTaxiOrdersListHandler::class,
        'Доставка'                         => DriverTaxiOrdersListHandler::class,
        '/order_delivery_process_([0-9]+)' => DriverProcessTaxiOrderHandler::class,

        '/my_hot_orders'              => DriverHotOrdersListHandler::class,
        'Горячие заказы'              => DriverHotOrdersListHandler::class,
        '/order_hot_process_([0-9]+)' => DriverProcessHotOrderHandler::class,

        'Подтвердить доставку'             => ConfirmTaxiOrdersListHandler::class,
        '/order_delivery_confirm_([0-9]+)' => ConfirmTaxiOrderHandler::class,

        '/order_check'    => OrderCheckHandler::class,
        '5'               => OrderCheckHandler::class,
        'Проверить заказ' => OrderCheckHandler::class,

        '/order_cancel'  => OrderCancelHandler::class,
        '6'              => OrderCancelHandler::class,
        'Отменить заказ' => OrderCancelHandler::class,

        '/last_order'     => OrderLastHandler::class,
        '4'               => OrderLastHandler::class,
        'Последний заказ' => OrderLastHandler::class,

        'Наход'           => OrderSuccessHandle::class,
        'Не наход'        => OrderSuccessHandle::class,

        // '/create_product_type' => CreateProductTypeHandler::class,
        // 'Создать товар' => CreateProductTypeHandler::class,
        '/create_product' => CreateProductHandler::class,
        'Создать клад'    => CreateProductHandler::class,

        '/my_products' => DriverProductListHandler::class,
        'Мои клады'    => DriverProductListHandler::class,

        '/clean_cache' => CleanCacheHandler::class,
        'Очистить кеш' => CleanCacheHandler::class,
    ];

    public static function getHandler(string $key): ?string
    {
        return self::MAP[$key] ?? null;
    }
}

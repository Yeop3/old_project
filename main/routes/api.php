<?php

//
//use App\Http\Controllers\AuthController;
//use App\Http\Controllers\BotLogicController;
//use App\Http\Controllers\ClientController;
//use App\Http\Controllers\DiscountController;
//use App\Http\Controllers\DispatchController;
//use App\Http\Controllers\DriverController;
//use App\Http\Controllers\LocationController;
//use App\Http\Controllers\MessagesController;
//use App\Http\Controllers\OperatorController;
//use App\Http\Controllers\OrderController;
//use App\Http\Controllers\PaymentMethodController;
//use App\Http\Controllers\ProductController;
//use App\Http\Controllers\ProductTypeController;
//use App\Http\Controllers\ProxyController;
//use App\Http\Controllers\SellerSettingController;
//use App\Http\Controllers\ShiftController;
//use App\Http\Controllers\StandardTelegramBotController;
//use App\Http\Controllers\StatisticController;
//use App\Http\Controllers\StokerController;
//use App\Http\Controllers\Wallet\CryptoTransactionController;
//use App\Http\Controllers\Wallet\CryptoWalletController;
//use App\Http\Controllers\Wallet\EasyPayTransactionController;
//use App\Http\Controllers\Wallet\EasyPayWalletController;
//use App\Http\Controllers\Wallet\GlobalMoneyWalletController;
//use App\Http\Controllers\Wallet\KunaCodeController;
//use App\Http\Controllers\Wallet\QiwiManualPaymentController;
//use App\Http\Controllers\Wallet\QiwiManualWalletController;

use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BotLogicController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\SellerSettingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StandardTelegramBotController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\Wallet\CryptoWalletController;
use App\Http\Controllers\Wallet\EasyPayWalletController;
use App\Http\Controllers\Wallet\GlobalMoneyWalletController;
use App\Http\Controllers\Wallet\QiwiManualPaymentController;
use App\Http\Controllers\Wallet\QiwiManualWalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('admin/login', [AuthAdminController::class, 'login']);
Route::post('login', [AuthController::class, 'login']);
Route::get('refresh', [AuthController::class, 'refresh']);
Route::get('auth/select', [OperatorController::class, 'getList']);

Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::namespace('Admin')->middleware('auth.admin')->group(function () {
    Route::get('sellers/statistics', [SellerStatisticController::class]);
//
    Route::apiResource('sellers', \SellerController::class);
    Route::put('sellers/ban/{id}', [SellerController::class, 'ban']);
    Route::put('sellers/unban/{id}', [SellerController::class, 'unban']);
    //Route::get('order/sellers', [OrderController::class, 'getSellers']);
});

Route::middleware('auth.seller')->group(function () {
    Route::get('payment_methods', [PaymentMethodController::class, 'index']);

    Route::get('product_types/for_select', [ProductTypeController::class, 'forSelect']);
    Route::apiResource('product_types', \ProductTypeController::class);
    Route::post('product_types/delete_mass', [ProductTypeController::class, 'destroyMass']);

    Route::get('drivers/permissions', [DriverController::class, 'getPermissions']);
    Route::get('drivers/for_select', [DriverController::class, 'forSelect']);
    Route::apiResource('drivers', \DriverController::class);
    Route::post('drivers/delete_mass', [DriverController::class, 'destroyMass']);

    Route::get('locations/for_select', [LocationController::class, 'forSelect']);
    Route::apiResource('locations', \LocationController::class);
    Route::post('locations/delete_mass', [LocationController::class, 'destroyMass']);

    Route::get('products/change-status', [ProductController::class, 'changeStatus']);
    Route::get('products/status', [ProductController::class, 'getStatus']);
    Route::post('products/actions-select', [ProductController::class, 'actionsSelect']);
    Route::apiResource('products', \ProductController::class);
    Route::post('products/create_mass', [ProductController::class, 'storeMass']);
    Route::post('products/delete_mass', [ProductController::class, 'destroyMass']);
    Route::delete('products/photos/{number}', [ProductController::class, 'destroyPhoto']);

    Route::apiResource('stokers', \StokerController::class);

    Route::apiResource('discounts', \DiscountController::class);

    Route::get('/bots/select', [StandardTelegramBotController::class, 'getSelect']);
    Route::apiResource('bots', \StandardTelegramBotController::class);
    Route::get('/bots/{number}/reinstall_webhook', [StandardTelegramBotController::class, 'reinstallWebhook']);

    Route::apiResource('bot_logics', \BotLogicController::class)
        ->except('store', 'show', 'update', 'delete');

    Route::get('bot_logics/{type}/{number}', [BotLogicController::class, 'show']);
    Route::post('bot_logics/{type}/{number}/clone', [BotLogicController::class, 'clone']);
    Route::put('bot_logics/client/{number}', [BotLogicController::class, 'update']);
    Route::delete('bot_logics/client/{number}', [BotLogicController::class, 'destroy']);

    Route::get('operators/for-select', [OperatorController::class, 'forSelect']);
    Route::get('operators/for-bots', [OperatorController::class, 'getListForBots']);
    Route::apiResource('operators', \OperatorController::class);

    Route::get('shifts', [ShiftController::class, 'index']);
    Route::get('shifts/export/current', [ShiftController::class, 'exportByCurrent']);
    Route::get('shifts/export/prev', [ShiftController::class, 'exportByPrev']);
    Route::get('shifts/export/{id}', [ShiftController::class, 'exportById']);
    Route::get('shifts/current', [ShiftController::class, 'current']);
    Route::post('shifts/start_new/{operator_number}', [ShiftController::class, 'startNew']);
    Route::get('shifts/{number}', [ShiftController::class, 'show']);

    Route::get('clients/history', [ClientController::class, 'getHistory']);
    Route::post('clients/send_message', [ClientController::class, 'sendMessageToClient']);
    Route::post('clients/{client_number}/ban', [ClientController::class, 'banClient']);
    Route::post('clients/{client_number}/un_ban', [ClientController::class, 'unBanClient']);
    Route::post('clients/{client_number}/black_list', [ClientController::class, 'blackListClient']);
    Route::post('clients/{client_number}/un_black_list', [ClientController::class, 'unBlackListClient']);
    Route::post('clients/multi-ban/', [ClientController::class, 'multiBan']);
    Route::post('clients/multi-black-list/', [ClientController::class, 'multiBlackList']);
    Route::post('clients/multi-delete/', [ClientController::class, 'multiDelete']);
    Route::post('clients/un_ban_all', [ClientController::class, 'unBanAll']);
    Route::get('clients/hand-dispatch-actual-telegram', [
        ClientController::class,
        'indexHandDispatchActualTelegram',
    ]);
    Route::get('clients/spam-reserve', [ClientController::class, 'spamReserve']);
    Route::get('clients/export_csv_telegram', [ClientController::class, 'exportToCsv']);
    Route::get(
        'clients/export_csv_telegram_actual_username',
        [ClientController::class, 'exportCsvTelegramActualUsername']
    );
    Route::get('clients/for_select', [ClientController::class, 'forSelect']);
    Route::apiResource('clients', \ClientController::class)
        ->only('index', 'show', 'update', 'destroy');

    Route::namespace('Wallet')->group(function () {
        Route::post(
            'wallets/qiwi_manual/deleted/clear',
            [QiwiManualWalletController::class, 'clearDeleted']
        );
        Route::get(
            'wallets/qiwi_manual/deleted/{number}',
            [QiwiManualWalletController::class, 'showDeleted']
        );
        Route::post(
            'wallets/qiwi_manual/deleted/{number}/restore',
            [QiwiManualWalletController::class, 'restore']
        );
        Route::get(
            'wallets/qiwi_manual/deleted',
            [QiwiManualWalletController::class, 'indexDeleted']
        );
        Route::delete(
            'wallets/qiwi_manual/{number}/forever',
            [QiwiManualWalletController::class, 'destroyForever']
        );
        Route::apiResource(
            'wallets/qiwi_manual',
            \QiwiManualWalletController::class,
            ['as' => 'wallets']
        );

        Route::get(
            'payments/qiwi_manual/phones-select',
            [QiwiManualPaymentController::class, 'phonesSelect']
        )
            ->name('qiwi_manual.phones_select');
        Route::apiResource(
            'payments/qiwi_manual',
            \QiwiManualPaymentController::class
        )
            ->except('destroy');

        Route::apiResource(
            'wallet/crypto',
            \CryptoWalletController::class,
            ['as' => 'wallets']
        );
        Route::get('/wallet/crypto/select', [
            CryptoWalletController::class,
            'getSelect',
        ]);

        Route::apiResource('transaction/crypto', \CryptoTransactionController::class)->only('index');

        Route::apiResource('kuna/accounts', \KunaCodeController::class);
        Route::apiResource('kuna/codes', \KunaCodeController::class)->only(['index', 'show']);

        Route::get('global-money/wallet/get-select', [GlobalMoneyWalletController::class, 'getSelect']);
        Route::post('global-money/wallet/access', [GlobalMoneyWalletController::class, 'checkAccess']);
        Route::apiResource('global-money/wallet', \GlobalMoneyWalletController::class);

        Route::apiResource('global-money/transaction', \GlobalMoneyWalletController::class)
            ->only(['show', 'index']);

        Route::get('easy-pay/wallet/get-select', [EasyPayWalletController::class, 'getSelect']);
        Route::post('easy-pay/wallet/restore-balance/{number}', [EasyPayWalletController::class, 'restoreBalance']);

        Route::post('easy-pay/wallet/check/{number}', [EasyPayWalletController::class, 'check']);
        Route::apiResource('easy-pay/wallet', \EasyPayWalletController::class, ['as' => 'wallet']);
        Route::apiResource('easy-pay/transaction', \EasyPayTransactionController::class, [
            'as' => 'transaction',
        ])->only(['show', 'index']);
    });

    Route::get(
        'order/for_select',
        [OrderController::class, 'forSelect']
    );
    Route::get(
        'order/calc_coming',
        [OrderController::class, 'calcComing']
    );

    Route::get('proxies/for-select', [
        ProxyController::class,
        'forSelect',
    ]);
    Route::apiResource('proxies', \ProxyController::class);

    Route::put('proxies/{proxy_number}', [
        ProxyController::class,
        'update',
    ])->name('proxies.update');

    Route::get('proxies/{proxy_number}', [
        ProxyController::class,
        'show',
    ])->name('proxies.show');

    Route::delete('proxies/{proxy_number}', [
        ProxyController::class,
        'destroy',
    ])->name('proxies.destroy');

    Route::get('order/', [OrderController::class, 'index']);
    Route::get('order/count_to_filter', [OrderController::class, 'getCountToFilter']);
    Route::get('order/count_to_filter_status', [OrderController::class, 'getOrderStatusCounter']);
    Route::get('order/status', [OrderController::class, 'getStatus']);
    Route::post('order/restoration_canceled_order', [OrderController::class, 'restorationCanceledOrder']);
    Route::post('order/restoration_paid_order', [OrderController::class, 'restorationPaidOrder']);
    Route::post('order/set_give_status/{id}', [OrderController::class, 'setStatusGive']);
    Route::post('order/set_canceled_by_operator_status/{id}', [OrderController::class, 'setCanceledByOperator']);
    Route::post('order/set_canceled_by_operator_status_all', [OrderController::class, 'cancelAll']);
    Route::post('order/set_canceled_by_operator_status_awaiting', [OrderController::class, 'cancelAwaiting']);
    Route::post('order/set_canceled_by_operator_status_partially', [OrderController::class, 'cancelPartially']);
    Route::get('order/crypto', [OrderController::class, 'getCrypto']);
    Route::get('orders/{id}', [OrderController::class, 'show']);

    Route::get('settings', [SellerSettingController::class, 'index']);
    Route::put('settings', [SellerSettingController::class, 'update']);

    Route::get('statistic/chart', [StatisticController::class, 'chart']);
    Route::apiResource('statistic', \StatisticController::class)->only('index', 'show');

    Route::apiResource('dispatches/', \DispatchController::class)
        ->only('store', 'index');
    Route::get(
        'dispatch/get-product-exist-text/{bot}',
        [
            DispatchController::class,
            'getProductExistText',
        ]
    );

    Route::get('messages/{client_number?}/{bot_number?}', [MessagesController::class, 'index']);
});

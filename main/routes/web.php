<?php

use App\Bot\Notifications\NotifyOrderPaid;
use App\Http\Controllers\BitapsCallbackController;
use App\Http\Controllers\BotWebHookController;
use App\Services\Wallet\EasyPayWallet\EasyPayApi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/webhook/{slug}', [BotWebHookController::class, 'bot'])
    ->name('bot.hook');
Route::post('/webhook/main/{slug}', [BotWebHookController::class, 'mainBot'])
    ->name('main.bot.hook');
Route::post('/bitaps/callback', [BitapsCallbackController::class])->name('bit.aps');

// Route::post('/callback123', function(\Illuminate\Http\Request $request) {
//     Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));
// });

 Route::get('/test', function () {
//     dd($asdsad);
//     app()->make(NotifyOrderPaid::class)->execute(\App\Models\Order::find(1));

//     $a = app()
//         ->make(EasyPayApi::class)
//         ->setLoginData(new \App\Services\Wallet\EasyPayWallet\VO\EasyPayLoginData(
//             '380502165607',
//             'wDVXj3xAHuetiHL',
//         ))
//         ->login()
//         ->getWallets()
//         ->getWalletStatements(2273245, 8, 2020)
//         ->getWalletTransactions(
//             23849160,
//             new \App\Services\Wallet\EasyPayWallet\VO\DateRange(now()->subDays(10), now()->addDay()),
//             new \App\Services\Wallet\EasyPayWallet\VO\PageData(1)
//         )
     //     dd($a);
 });

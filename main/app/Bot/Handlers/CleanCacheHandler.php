<?php
/**
 * Created by PhpStorm.
 * User: Aios
 * https://t.me/aiosslike
 */

namespace App\Bot\Handlers;


use App\Models\Bot;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Class CleanCacheHandler
 * @package App\Bot\Handlers
 */
final class CleanCacheHandler implements BotHandler {

    /**
     * @param BotMan $botMan
     * @param Bot $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        Artisan::call('cache:clear redis');
        Artisan::call('cache:clear file');
        $botMan->reply('Кеш успешно очищен.');
    }
}

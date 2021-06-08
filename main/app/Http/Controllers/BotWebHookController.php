<?php

namespace App\Http\Controllers;

use App\Bot\Bot;
use App\MainBot\Bot as mBot;
use App\Models\MainBot;
use App\Models\Seller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class BotWebHookController.
 */
class BotWebHookController extends Controller
{
    /**
     * @param Request $request
     * @param $slug
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function bot(Request $request, $slug): void
    {
        $botModel = \App\Models\Bot::whereSlug($slug)->firstOrFail();

        $telegramId = $request->get('message')['from']['id'] ?? null;

        $isNeedToStop = $this->isNeedToStop($botModel, $telegramId);

        if ($isNeedToStop) {
            return;
        }

        $seller = Seller::whereId($botModel->seller_id)->firstOrFail();

        if ($seller->banned === 1) {
            return;
        }

        if (!$botModel->active) {
            return;
        }

        $bot = new Bot($botModel, $request);

        $bot->webHook();
    }

    /**
     * @param Request $request
     * @param $slug
     */
    public function mainBot(Request $request, $slug): void
    {
        $botModel = MainBot::whereSlug($slug)->firstOrFail();

        if (!$botModel->active) {
            return;
        }

        $bot = new mBot($botModel, $request);

        $bot->webHook();
    }

    /**
     * @param \App\Models\Bot $botModel
     * @param string|null     $telegramId
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    private function isNeedToStop(\App\Models\Bot $botModel, ?string $telegramId): bool
    {
        $rateLimitEnable = config('features.bot_callback_rate_limit.enable');

        if (!$rateLimitEnable || !$telegramId) {
            return false;
        }

        $rateLimitKey = "throttle_bot:$botModel->id:$telegramId";

        $isHandling = Cache::store('redis')->get($rateLimitKey);

        if ($isHandling) {
            return true;
        }

        $ttl = config('features.bot_callback_rate_limit.ttl');

        Cache::store('redis')->put($rateLimitKey, true, now()->addSeconds((int) $ttl));

        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Bot\Middleware;

use App\Bot\GetCommonButtons;
use App\Models\Bot;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;

/**
 * Class AttachCommonButtonsMiddleware.
 */
final class AttachCommonButtonsMiddleware implements Sending
{
    private Bot              $botModel;
    private GetCommonButtons $getCommonButtons;

    public function __construct(Bot $botModel, GetCommonButtons $getCommonButtons)
    {
        $this->botModel = $botModel;
        $this->getCommonButtons = $getCommonButtons;
    }

    /**
     * @param mixed    $payload
     * @param callable $next
     * @param BotMan   $bot
     *
     * @return mixed
     */
    public function sending($payload, $next, BotMan $bot)
    {
        $replyMarkup = json_decode($payload['reply_markup'] ?? '{}', true);

        if (!$replyMarkup) {
            $replyMarkup = [];
        }

        $replyMarkup['resize_keyboard'] = true;

        if (!isset($replyMarkup['inline_keyboard'])) {
            $replyMarkup['keyboard'] = [
                ...($replyMarkup['keyboard'] ?? []),
                ...($this->getCommonButtons)($bot, $this->botModel),
            ];
        }

        $payload['reply_markup'] = json_encode($replyMarkup);

        return $next($payload);
    }
}

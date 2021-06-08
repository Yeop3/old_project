<?php

declare(strict_types=1);

namespace App\Bot\Middleware;

use App\Bot\Handlers\ClientHandler;
use App\Models\Bot;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

/**
 * Class HandleClientMiddleware.
 */
final class HandleClientMiddleware implements Heard
{
    private ClientHandler $handleClient;
    private Bot           $botModel;

    /**
     * HandleClientMiddleware constructor.
     *
     * @param ClientHandler $handleClient
     * @param Bot           $botModel
     */
    public function __construct(ClientHandler $handleClient, Bot $botModel)
    {
        $this->handleClient = $handleClient;
        $this->botModel = $botModel;
    }

    /**
     * Handle a message that was successfully heard, but not processed yet.
     *
     * @param IncomingMessage $message
     * @param callable        $next
     * @param BotMan          $bot
     *
     * @return mixed
     */
    public function heard(IncomingMessage $message, $next, BotMan $bot)
    {
        $this->handleClient->execute($bot, $this->botModel);

        return $next($message);
    }
}

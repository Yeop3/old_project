<?php

declare(strict_types=1);

namespace App\Bot\Middleware;

use App\Events\SellerBotMessageSent;
use App\Models\Bot;
use App\Services\Bot\BotMessageLogger\MessageFromBotDto;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class LogMiddleware.
 */
final class LogMiddleware implements Sending
{
    private Bot            $botModel;
    private ClientResolver $clientResolver;
    private Dispatcher     $dispatcher;

    public function __construct(Bot $botModel, ClientResolver $clientResolver, Dispatcher $dispatcher)
    {
        $this->botModel = $botModel;
        $this->clientResolver = $clientResolver;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle an outgoing message payload before/after it
     * hits the message service.
     *
     * @param mixed    $payload
     * @param callable $next
     * @param BotMan   $bot
     *
     * @return mixed
     */
    public function sending($payload, $next, BotMan $bot)
    {
        $text = $payload['text'] ?? $payload['caption'] ?? $next($payload);

        if (!$text) {
            return $next($payload);
        }

        try {
            $client = $this->clientResolver
                ->resolve(
                    $bot->getUser(),
                    $bot->getDriver()->getName(),
                    $this->botModel->seller_id
                );

            $this->dispatcher->dispatch(
                new SellerBotMessageSent(
                    $client,
                    new MessageFromBotDto(
                        $text,
                        [],
                        [],
                        null
                    ),
                )
            );
        } catch (Throwable $e) {
            $client = null;
            Log::error(json_encode([
                'file'    => $e->getFile(),
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]));

            return $next($payload);
        }

        return $next($payload);
    }
}

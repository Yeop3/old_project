<?php

declare(strict_types=1);

namespace App\MainBot;

use App\MainBot\Handlers\AliveHandler;
use App\MainBot\Handlers\SellerCreateHandler;
use App\MainBot\Handlers\ShowSellersHandler;

/**
 * Class HandlersMapper.
 */
final class HandlersMapper
{
    public const MAP = [
        'ANYKEY'         => ShowSellersHandler::class,
        '/alive'         => AliveHandler::class,
        '/seller_create' => SellerCreateHandler::class,
    ];

    public static function getHandler(string $key): ?string
    {
        return self::MAP[$key] ?? null;
    }
}

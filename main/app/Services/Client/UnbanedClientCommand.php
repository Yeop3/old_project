<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Support\Collection;

/**
 * Class UnbanedClientCommand.
 */
final class UnbanedClientCommand
{
    public function execute(): void
    {
        Client::whereNotNull('ban_expires_at')->chunk(
            100,
            static function (Collection $client) {
                $client->each(static function (Client $client) {
                    $now = now();
                    if ($now->gte($client->ban_expires_at)) {
                        $client->ban_expires_at = null;
                        $client->save();
                    }
                });
            }
        );
    }
}

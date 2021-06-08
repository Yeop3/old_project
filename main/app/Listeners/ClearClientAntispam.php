<?php

namespace App\Listeners;

use App\Bot\Commands\AntiSpamChecker;
use App\Events\Order\OrderPaid;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class ClearClientAntispam.
 */
class ClearClientAntispam
{
    private AntiSpamChecker $antiSpamChecker;

    public function __construct(AntiSpamChecker $antiSpamChecker)
    {
        $this->antiSpamChecker = $antiSpamChecker;
    }

    public function handle(OrderPaid $event): void
    {
        try {
            $this->antiSpamChecker->cacheClear($event->getOrder()->client, $event->getOrder()->source);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getFile(), $e->getLine()]);
        }
    }
}

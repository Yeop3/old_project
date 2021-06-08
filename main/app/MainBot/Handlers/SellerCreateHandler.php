<?php

declare(strict_types=1);

namespace App\MainBot\Handlers;

use App\MainBot\Conversations\SellerCreateConversation;
use App\Models\MainBot;
use BotMan\BotMan\BotMan;

/**
 * Class SellerCreateHandler.
 */
class SellerCreateHandler
{
    public function execute(BotMan $botMan, MainBot $botModel, ...$params): void
    {
        $botMan1 = $botMan;

        $botMan1->startConversation(new SellerCreateConversation(), $botMan->getUser()->getId());
    }
}

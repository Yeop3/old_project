<?php

namespace App\Listeners\Order;

use App\Bot\Commands\AntiSpamChecker;
use App\Events\Payment\WrongPaymentCodeGot;
use Exception;

/**
 * Class CheckOrderWrongCodesFrequency.
 */
class CheckOrderWrongCodesFrequency
{
    private AntiSpamChecker $antiSpamHandler;

    public function __construct(AntiSpamChecker $antiSpamHandler)
    {
        $this->antiSpamHandler = $antiSpamHandler;
    }

    /**
     * @param WrongPaymentCodeGot $event
     *
     * @throws Exception
     */
    public function handle(WrongPaymentCodeGot $event): void
    {
        $this->antiSpamHandler->handleFrequentWrongCodes($event->getClient());
    }
}

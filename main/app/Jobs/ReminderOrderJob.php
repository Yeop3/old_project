<?php

namespace App\Jobs;

use App\Models\BotLogic\BotLogic;
use App\Models\BotLogic\BotLogicReminder;
use App\Models\Order;
use App\Services\Order\ReminderOrderHandler;
use App\Services\Order\VO\OrderStatus;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ReminderOrderJob.
 */
final class ReminderOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private BotLogic    $botLogic;
    private Order       $order;
    private OrderStatus $orderStatus;
    private int         $posTime;
    private int         $maxTime;

    /**
     * Create a new job instance.
     *
     * @param Order    $order
     * @param int      $posTime
     * @param BotLogic $botLogic
     * @param int      $maxTime
     */
    public function __construct(Order $order, int $posTime, BotLogic $botLogic, int $maxTime)
    {
        $this->order = $order;
        $this->posTime = $posTime;
        $this->botLogic = $botLogic;
        $this->maxTime = $maxTime;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle(): void
    {
        $orderStatus = $this->order->status->getValue();
        $client = $this->order->client;
        if (
            ($orderStatus === OrderStatus::STATUS_AWAITING_PAYMENT ||
                $orderStatus === OrderStatus::STATUS_PARTIALLY_PAID) &&
            !$client->ban_expires_at
        ) {
            app(ReminderOrderHandler::class)->handle($this->order);
            $this->getNextTime();
        }
    }

    private function getNextTime(): void
    {
        $orderStatus = $this->order->status->getValue();
        $botLogicReminder = BotLogicReminder::where(
            'key',
            $orderStatus === OrderStatus::STATUS_AWAITING_PAYMENT ?
                'payment' :
                'payment_partially'
        )->whereBotLogicId(
            $this->botLogic->id
        )->firstOr();

        $options = collect($botLogicReminder->options);
        $intervals = $options->first(fn ($value) => $value['key'] === 'intervals')['value'];

        /**
         * Menyal i kagetsya kakyuto xuinuy sdelal.
         * TODO Review: @PavelGrom.
         */
        $intervalsTimes = collect(explode(',', $intervals))->map(fn ($value) => (int) $value);

        if (($this->maxTime !== $this->posTime) && $intervalsTimes->count()) {
            self::dispatch(
                $this->order,
                $this->posTime + 1,
                $this->botLogic,
                count($intervalsTimes) - 1
            )->delay(
                now()->addMinutes(
                    (int) $intervalsTimes[$this->posTime]
                )
            );
        }
    }
}

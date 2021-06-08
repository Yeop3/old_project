<?php

namespace App\Providers;

use App\Events\ClientBannedEvent;
use App\Events\ClientUnbannedEvent;
use App\Events\GlobalMoney\GlobalMoneyLoginFailed;
use App\Events\Order\OrderCanceledByClient;
use App\Events\Order\OrderCanceledByOperator;
use App\Events\Order\OrderCanceledByTimeout;
use App\Events\Order\OrderPaid;
use App\Events\Order\OrderPartiallyPaid;
use App\Events\Order\OrderSavingEvent;
use App\Events\Order\ReminderOrder;
use App\Events\Payment\WrongPaymentCodeGot;
use App\Events\SellerBotMessageReceived;
use App\Events\SellerBotMessageSent;
use App\Events\StokerProductTypeNotifyEvent;
use App\Events\Wallet\EasyPayLoginFailEvent;
use App\Listeners\Bot\NotifyClientOrderCanceledByTimeout;
use App\Listeners\Bot\NotifyClientOrderPaid;
use App\Listeners\Bot\NotifyClientOrderPartiallyPaid;
use App\Listeners\Bot\NotifyOperatorOrderCancel;
use App\Listeners\Bot\NotifyReminderOrder;
use App\Listeners\CheckBotMessagesFrequency;
use App\Listeners\ClearClientAntispam;
use App\Listeners\ClientBannedListener;
use App\Listeners\ClientUnbannedListener;
use App\Listeners\GlobalMoney\GlobalMoneyLoginFailedListener;
use App\Listeners\LogBotMessage;
use App\Listeners\LogClientMessage;
use App\Listeners\NotifyStokerProductType;
use App\Listeners\Order\CheckOrderCancelFrequency;
use App\Listeners\Order\CheckOrderWrongCodesFrequency;
use App\Listeners\Order\OrderSavingListener;
use App\Listeners\Wallet\EasyPayLoginFailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPaid::class => [
            NotifyClientOrderPaid::class,
            ClearClientAntispam::class,
        ],
        OrderPartiallyPaid::class => [
            NotifyClientOrderPartiallyPaid::class,
        ],
        OrderCanceledByTimeout::class => [
            NotifyClientOrderCanceledByTimeout::class,
            CheckOrderCancelFrequency::class,
        ],
        OrderCanceledByClient::class => [
            CheckOrderCancelFrequency::class,
        ],
        OrderCanceledByOperator::class => [
            NotifyOperatorOrderCancel::class,
        ],
        ReminderOrder::class => [
            NotifyReminderOrder::class,
        ],
        OrderSavingEvent::class => [
            OrderSavingListener::class,
        ],
        GlobalMoneyLoginFailed::class => [
            GlobalMoneyLoginFailedListener::class,
        ],
        SellerBotMessageReceived::class => [
            CheckBotMessagesFrequency::class,
            LogClientMessage::class,
        ],
        SellerBotMessageSent::class => [
            LogBotMessage::class,
        ],
        WrongPaymentCodeGot::class => [
            CheckOrderWrongCodesFrequency::class,
        ],
        EasyPayLoginFailEvent::class => [
            EasyPayLoginFailListener::class,
        ],
        ClientBannedEvent::class => [
            ClientBannedListener::class,
        ],
        ClientUnbannedEvent::class => [
            ClientUnbannedListener::class,
        ],
        StokerProductTypeNotifyEvent::class => [
            NotifyStokerProductType::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

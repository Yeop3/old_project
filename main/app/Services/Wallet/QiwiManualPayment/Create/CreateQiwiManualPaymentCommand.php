<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManualPayment\Create;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Shift;
use App\Models\Wallet\QiwiManualPayment;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Order\OrderPaymentHandler;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateQiwiManualPaymentCommand.
 */
final class CreateQiwiManualPaymentCommand
{
    private OrderPaymentHandler $orderPaymentHandler;

    public function __construct(OrderPaymentHandler $orderPaymentHandler)
    {
        $this->orderPaymentHandler = $orderPaymentHandler;
    }

    public function execute(Seller $seller, CreateQiwiManualPaymentDto $dto): QiwiManualPayment
    {
        $order = Order::whereSellerId($seller->id)->whereNumber($dto->getOrderNumber())->firstOrFail();
        $shift = Shift::whereNull('ended_at')->first();
        /* @var QiwiManualWallet $wallet */
        $wallet = $order->wallet;

        $payment = new QiwiManualPayment();

        $payment->seller_id = $seller->id;
        $payment->order_id = $order->id;
        $payment->qiwi_manual_wallet_id = $wallet->id;
        $payment->amount = $dto->getAmount();
        $payment->client_wallet = optional($dto->getClientWallet())->getValue();
        $payment->comment = $dto->getComment();
        $payment->shift_id = $shift->id ?? null;

        DB::beginTransaction();

        $payment->save();

        $this->orderPaymentHandler->handle($order, $payment->amount);

        DB::commit();

        return $payment;
    }
}

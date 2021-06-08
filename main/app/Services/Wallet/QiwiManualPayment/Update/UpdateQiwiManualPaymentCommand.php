<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManualPayment\Update;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Wallet\QiwiManualPayment;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateQiwiManualPaymentCommand.
 */
final class UpdateQiwiManualPaymentCommand
{
    public function execute(int $qiwiManualPaymentNumber, Seller $seller, UpdateQiwiManualPaymentDto $dto): QiwiManualPayment
    {
        $payment = QiwiManualPayment::whereSellerId($seller->id)->whereNumber($qiwiManualPaymentNumber)->firstOrFail();

        /** @var Order $order */
        $order = $payment->order;

        // TODO нельзя менять сумму оплаты если заказ завершен/отменен?
//        if ($order->status->getValue() !== OrderStatus::STATUS_PARTIALLY_PAID) {
//
//        }

        DB::beginTransaction();

        $order->unpaid_amount = $order->unpaid_amount->add($payment->amount)->subtract($dto->getAmount());
        $order->save();

        $payment->amount = $dto->getAmount();
        $payment->client_wallet = optional($dto->getClientWallet())->getValue();
        $payment->comment = $dto->getComment();

        $payment->save();

        DB::commit();

        return $payment;
    }
}

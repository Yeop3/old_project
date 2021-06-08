<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitaps;

use App\Models\Order;
use App\Models\Shift;
use App\Models\Wallet\CryptoTransaction;
use App\Services\Order\OrderPaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;

/**
 * Class BitapsCallbackHandler.
 */
final class BitapsCallbackHandler
{
    private OrderPaymentHandler $orderPaymentHandler;

    public function __construct(OrderPaymentHandler $orderPaymentHandler)
    {
        $this->orderPaymentHandler = $orderPaymentHandler;
    }

    public function handle(Request $request): void
    {
        $event = $request->get('event');

        if ($event !== 'confirmed') {
            return;
        }

        $invoice = $request->get('invoice');

        if (!$invoice) {
            return;
        }

        /** @var Order $order */
        $order = Order::where('bitaps_invoice', $request->get('invoice'))->first();

        if (!$order) {
            return;
        }

        $amount = $request->get('amount');

        $currency = new Currency($request->get('currency'));

        $cryptoAmount = new Money($amount, $currency);

        $uahAmount = convertToRubFromCrypto($cryptoAmount, $currency, (float) $order->crypto_uah_rate);

        DB::beginTransaction();

        $this->orderPaymentHandler->handle($order, $uahAmount);

        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $transaction = new CryptoTransaction();

        $transaction->seller_id = $order->seller_id;
        $transaction->crypto_wallet_id = $order->wallet->id;
        $transaction->order_id = $order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $uahAmount;
        $transaction->address = $request->get('address');
        $transaction->hash = $request->get('tx_hash');

        $transaction->save();

        DB::commit();
    }
}

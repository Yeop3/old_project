<?php

declare(strict_types=1);

namespace App\Services\Wallet\Kuna;

use App\Models\Client;
use App\Models\Proxy;
use App\Models\Shift;
use App\Models\Wallet\KunaCode;
use App\Services\Order\OrderPaymentHandler;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeCurrencyIsNotUahException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeIsNotActiveException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeNotFoundException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeRecepientIsNotAllException;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;

/**
 * Class HandleKunaCodeCommand.
 */
final class HandleKunaCodeCommand
{
    private KunaApi $kunaApi;
    private OrderPaymentHandler $orderPaymentHandler;

    public function __construct(KunaApi $kunaApi, OrderPaymentHandler $orderPaymentHandler)
    {
        $this->kunaApi = $kunaApi;
        $this->orderPaymentHandler = $orderPaymentHandler;
    }

    public function execute(Client $client, string $code): void
    {
        if (!$client->order) {
            return;
        }

        if ($client->order->getWalletType()->getValue() !== WalletType::TYPE_KUNA_CODE) {
            return;
        }

        $this->checkCode($code, $client->order->wallet->proxy);

        $codeActivation = $this->kunaApi->activateCode(
            $code,
            $client->order->wallet->public_key,
            $client->order->wallet->private_key,
            $client->order->wallet->proxy
        );

        $amount = new Money($codeActivation['amount'] * 100, new Currency('UAH'));

        DB::beginTransaction();

        $this->storeKunaCode($client, $amount, $code, $codeActivation);

        $this->orderPaymentHandler->handle($client->order, $amount);

        DB::commit();
    }

    private function checkCode(string $code, ?Proxy $proxy = null): void
    {
        $check = $this->kunaApi->checkCode($code, $proxy);

        if (($check['messages'][0] ?? null) === 'code_not_found') {
            throw new KunaCodeNotFoundException();
        }

        if (($check['status'] ?? null) !== 'active') {
            throw new KunaCodeIsNotActiveException();
        }

        if (($check['currency'] ?? null) !== 'uah') {
            throw new KunaCodeCurrencyIsNotUahException();
        }

        if (($check['recipient'] ?? null) !== 'all') {
            throw new KunaCodeRecepientIsNotAllException();
        }
    }

    private function storeKunaCode(Client $client, Money $amount, string $code, array $codeActivation): void
    {
        $shift = Shift::whereSellerId($client->order->seller_id)->whereNull('ended_at')->first();

        $kunaCode = new KunaCode();

        $kunaCode->seller_id = $client->order->seller_id;
        $kunaCode->kuna_account_id = $client->order->wallet->id;
        $kunaCode->order_id = $client->order->id;
        $kunaCode->shift_id = $shift->id ?? null;
        $kunaCode->amount = $amount;
        $kunaCode->code = $code;
        $kunaCode->internal_id = $codeActivation['id'];
        $kunaCode->sn = $codeActivation['sn'];

        $kunaCode->save();
    }
}

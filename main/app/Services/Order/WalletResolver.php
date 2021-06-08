<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Seller;
use App\Models\SellerSetting;
use App\Models\Wallet\CryptoWallet;
use App\Models\Wallet\EasyPayWallet;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Models\Wallet\KunaAccount;
use App\Models\Wallet\QiwiManualWallet;
use App\Models\Wallet\Wallet;
use App\Services\Wallet\Exceptions\RotateWalletsAreBusyException;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\Services\Wallet\VO\CryptoWalletResolvingType;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

/**
 * Class WalletResolver.
 */
final class WalletResolver
{
    public function resolve(Seller $seller, PaymentMethod $paymentMethod): Wallet
    {
        $type = WalletType::createByPaymentMethod($paymentMethod);

        if ($type->getModelClass() === QiwiManualWallet::class) {
            return QiwiManualWallet::whereSellerId($seller->id)
                ->inRandomOrder()
                ->where('active', 1)
                ->first();
        }

        if ($type->getModelClass() === CryptoWallet::class) {
            return $this->resoleCryptoWallet($seller, $paymentMethod);
        }

        if ($type->getModelClass() === KunaAccount::class) {
            return KunaAccount::whereSellerId($seller->id)
                ->inRandomOrder()
                ->where('active', 1)
                ->first();
        }

        if ($type->getModelClass() === GlobalMoneyWallet::class) {
            return GlobalMoneyWallet::whereSellerId($seller->id)
                ->inRandomOrder()
                ->where('active', 1)
                ->get()
                ->random();
        }

        if ($type->getModelClass() === EasyPayWallet::class) {
            $wallet = EasyPayWallet::whereSellerId($seller->id)
                ->with([
                    'transactions.order',
                ])
                ->inRandomOrder()
                ->where('active', 1)
                ->where('wrong_creadentials', 0)
                ->get()
                ->filter(function (EasyPayWallet $easyPayWallet) {
                    return !$easyPayWallet->is_limit;
                });

            if ($wallet->isEmpty()) {
                throw new RuntimeException('Wallet not found');
            }

            return $wallet->random();
        }

        throw new RuntimeException('unknown wallet type');
    }

    private function resoleCryptoWallet(Seller $seller, PaymentMethod $paymentMethod): ?CryptoWallet
    {
        $resolvingType = SellerSetting::whereSellerId($seller->id)
            ->where('key', 'wallets_resolving_type')
            ->first()
            ->value;

        $wallet = CryptoWallet::whereSellerId($seller->id)
            ->where('currency', $paymentMethod->getCurrency())
            ->when(
                $resolvingType === CryptoWalletResolvingType::ONLY_ROTATE,
                fn (Builder $builder) => $builder
                    ->where('payment_type', CryptoWalletPaymentType::ROTATE)
            )
            ->when(
                $resolvingType === CryptoWalletResolvingType::ONLY_BITAPS,
                fn (Builder $builder) => $builder
                    ->where('payment_type', CryptoWalletPaymentType::BITAPS)
            )
            ->orderBy('created_at', 'asc')
            ->orderBy('payment_type', 'asc')
            ->get()
            ->filter(
                fn (CryptoWallet $wallet) => $wallet->payment_type->getValue() === CryptoWalletPaymentType::BITAPS
                    || $wallet->ordersWaitingPayment->count() === 0
            )
            ->first();

        if (!$wallet) {
            throw new RotateWalletsAreBusyException();
        }

        return $wallet;
    }
}

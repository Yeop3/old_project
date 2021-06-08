<?php

declare(strict_types=1);

namespace App\Services\Wallet\Crypto\Create;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\CryptoWallet;

/**
 * Class CryptoWalletCreateCommand.
 */
final class CryptoWalletCreateCommand
{
    public function execute(CryptoWalletCreateDto $dto, Seller $seller): CryptoWallet
    {
        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($dto->getProxyNumber())->first();

        $wallet = new CryptoWallet();

        $wallet->seller_id = $seller->id;
        $wallet->name = $dto->getName();
        $wallet->currency = $dto->getCurrency();
        $wallet->payment_type = $dto->getPaymentType();
        $wallet->proxy_id = $proxy->id ?? null;
        $wallet->address = $dto->getAddress();
        $wallet->comment = $dto->getNote();
        $wallet->confirmations = $dto->getConfirmations();
        $wallet->save();

        return $wallet;
    }
}

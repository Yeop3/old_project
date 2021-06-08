<?php

declare(strict_types=1);

namespace App\Services\Wallet\Crypto\Update;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\CryptoWallet;

/**
 * Class CryptoWalletUpdateCommand.
 */
final class CryptoWalletUpdateCommand
{
    public function execute(CryptoWalletUpdateDto $dto, int $number, Seller $seller): CryptoWallet
    {
        $wallet = CryptoWallet::whereSellerId($seller->id)
            ->where('number', $number)
            ->firstOrFail();

        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($dto->getProxyNumber())->first();

        $wallet->name = $dto->getName();
        $wallet->proxy_id = $proxy->id ?? null;
        $wallet->comment = $dto->getNote();
        $wallet->confirmations = $dto->getConfirmations();
        $wallet->save();

        return $wallet;
    }
}

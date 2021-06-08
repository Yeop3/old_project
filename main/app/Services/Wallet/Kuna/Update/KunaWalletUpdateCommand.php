<?php

namespace App\Services\Wallet\Kuna\Update;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\KunaAccount;

/**
 * Class KunaWalletUpdateCommand.
 */
final class KunaWalletUpdateCommand
{
    public function execute(KunaWalletUpdateDto $dto, int $number, Seller $seller): KunaAccount
    {
        $wallet = KunaAccount::whereSellerId($seller->id)
            ->where('number', $number)
            ->firstOrFail();

        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($dto->getProxyNumber())->first();

        $wallet->name = $dto->getName();
        $wallet->comment = $dto->getComment();
        $wallet->active = $dto->getActive();
        $wallet->public_key = $dto->getPublicKey();
        $wallet->private_key = $dto->getPrivateKey();
        $wallet->proxy_id = $proxy->id ?? null;
        $wallet->save();

        return $wallet;
    }
}

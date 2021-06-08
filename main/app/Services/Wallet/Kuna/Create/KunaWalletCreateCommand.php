<?php

declare(strict_types=1);

namespace App\Services\Wallet\Kuna\Create;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\KunaAccount;

/**
 * Class KunaWalletCreateCommand.
 */
final class KunaWalletCreateCommand
{
    public function execute(KunaWalletCreateDto $dto, Seller $seller): KunaAccount
    {
        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($dto->getProxyNumber())->first();

        $wallet = new KunaAccount();

        $wallet->seller_id = $seller->id;
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

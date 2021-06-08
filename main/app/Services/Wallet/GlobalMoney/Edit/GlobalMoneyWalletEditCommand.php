<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\Edit;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Services\Wallet\GlobalMoney\GlobalMoneyApi;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;

/**
 * Class GlobalMoneyWalletEditCommand.
 */
final class GlobalMoneyWalletEditCommand
{
    private GlobalMoneyApi $api;

    public function __construct(GlobalMoneyApi $api)
    {
        $this->api = $api;
    }

    public function execute(GlobalMoneyWalletEditDto $dto, Seller $seller, int $number): GlobalMoneyWallet
    {
        $globalMoney = GlobalMoneyWallet::whereSellerId($seller->id)->where('number', $number)->firstOrFail();

        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($dto->getProxyNumber())->first();

        $globalMoney->name = $dto->getName();
        $globalMoney->wallet_number = $dto->getWalletNumber();
        $globalMoney->login = $dto->getLogin();
        $globalMoney->proxy_id = $proxy->id ?? null;
        $globalMoney->type = $dto->getType();
        $globalMoney->active = $dto->isActive();
        $globalMoney->wrong_credentials = 0;

        if ($dto->getPassword()) {
            $globalMoney->password = sha1($dto->getPassword());
        }

        $this->api->setLoginData(new GlobalMoneyLoginData($globalMoney->login, $globalMoney->password, $globalMoney->type));
        $this->api->login();

        $globalMoney->save();

        return $globalMoney;
    }
}

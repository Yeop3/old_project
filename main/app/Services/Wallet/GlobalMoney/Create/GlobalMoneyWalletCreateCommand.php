<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\Create;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Services\Wallet\GlobalMoney\GlobalMoneyApi;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;

/**
 * Class GlobalMoneyWalletCreateCommand.
 */
final class GlobalMoneyWalletCreateCommand
{
    private GlobalMoneyApi $api;

    public function __construct(GlobalMoneyApi $api)
    {
        $this->api = $api;
    }

    public function execute(GlobalMoneyCreateDto $createDto, Seller $seller): GlobalMoneyWallet
    {
        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($createDto->getProxyNumber())->first();

        $global = new GlobalMoneyWallet();
        $global->name = $createDto->getName();
        $global->wallet_number = $createDto->getWalletNumber();
        $global->login = $createDto->getLogin();
        $global->password = $createDto->getPassword();
        $global->type = $createDto->getType();
        $global->active = $createDto->isActive();
        $global->proxy_id = $proxy->id ?? null;
        $global->seller_id = $seller->id;

        $this->api->setLoginData(new GlobalMoneyLoginData($global->login, $global->password, $global->type));
        $this->api->login();

        $global->wrong_credentials = 0;

        $global->save();

        return $global;
    }
}

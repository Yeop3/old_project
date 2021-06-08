<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\Create;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\EasyPayWallet;
use App\Services\Wallet\EasyPayWallet\EasyPayApi;

/**
 * Class EasyPayWalletCreateCommand.
 */
final class EasyPayWalletCreateCommand
{
    private EasyPayApi $easyPayApi;

    /**
     * EasyPayWalletCreateCommand constructor.
     *
     * @param EasyPayApi $easyPayApi
     */
    public function __construct(EasyPayApi $easyPayApi)
    {
        $this->easyPayApi = $easyPayApi;
    }

    public function execute(EasyPayWalletCreateDto $getDto, Seller $seller): EasyPayWallet
    {
        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($getDto->getProxyNumber())->first();

        $easyPay = new EasyPayWallet();
        $easyPay->password = $getDto->getPassword();
        $easyPay->name = $getDto->getName();
        $easyPay->phone = $getDto->getPhone();
        $easyPay->wallet_number = $getDto->getWalletNumber();
        $easyPay->proxy_id = $proxy->id ?? null;
        $easyPay->seller_id = $seller->id;
        $easyPay->limit = $getDto->getLimit();

        $this->easyPayApi->setProxy($proxy);

        $this->easyPayApi->setLoginData($easyPay->login_data);

        $walletInfo = collect($this->easyPayApi->login()->getWallets())
            ->filter(fn ($el) => $el['number'] === $getDto->getWalletNumber())
            ->first();

        $easyPay->external_id = $walletInfo['id'] ?? 1;
        $easyPay->instrument_id = $walletInfo['instrumentId'] ?? 1;

        $easyPay->save();

        return $easyPay;
    }
}

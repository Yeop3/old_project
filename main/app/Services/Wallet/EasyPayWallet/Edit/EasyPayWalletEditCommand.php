<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\Edit;

use App\Models\Proxy;
use App\Models\Seller;
use App\Models\Wallet\EasyPayWallet;
use App\Services\Wallet\EasyPayWallet\EasyPayApi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

/**
 * Class EasyPayWalletEditCommand.
 */
final class EasyPayWalletEditCommand
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

    /**
     * @param EasyPayEditWalletDto $getDto
     * @param Seller               $seller
     * @param int                  $id
     *
     * @return EasyPayWallet|Builder|Model
     */
    public function execute(EasyPayEditWalletDto $getDto, Seller $seller, int $id)
    {
        $proxy = Proxy::whereSellerId($seller->id)->whereNumber($getDto->getProxyNumber())->firstOrFail();

        $easyPay = EasyPayWallet::whereSellerId($seller->id)->where('number', $id)->firstOrFail();
        if ($getDto->getPassword()) {
            $easyPay->password = $getDto->getPassword();
        }
        $easyPay->name = $getDto->getName();
        $easyPay->phone = $getDto->getPhone();
        $easyPay->wallet_number = $getDto->getWalletNumber();
        $easyPay->proxy_id = $proxy->id ?? null;
        $easyPay->limit = new Money($getDto->getLimit(), new Currency('UAH'));

        $this->easyPayApi->setProxy($proxy);
        $this->easyPayApi->setLoginData($easyPay->login_data);


        $newEasyPayData = $this->easyPayApi->login();

        $walletsInfo = collect($newEasyPayData->getWallets());
        $walletInfo = $walletsInfo->filter(fn ($el) => $el['number'] === $getDto->getWalletNumber())
            ->first();

        $easyPay->external_id = $walletInfo['id'];
        $easyPay->instrument_id = $walletInfo['instrumentId'];
        $easyPay->wrong_creadentials = 0;

        $easyPay->save();

        return $easyPay;
    }
}

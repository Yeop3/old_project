<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

use App\Models\Seller;
use App\Models\Wallet\EasyPayWallet;

/**
 * Class CheckAccountLoginCommand.
 */
final class CheckAccountLoginCommand
{
    private EasyPayApi $api;

    /**
     * CheckAccountLoginCommand constructor.
     *
     * @param EasyPayApi $api
     */
    public function __construct(EasyPayApi $api)
    {
        $this->api = $api;
    }

    /**
     * @param int    $number
     * @param Seller $seller
     *
     * @return EasyPayWallet
     */
    public function execute(int $number, Seller $seller): EasyPayWallet
    {
        $easyPay = EasyPayWallet::whereSellerId($seller->id)->where('number', $number)
            ->with('proxy')
            ->firstOrFail();

        $this->api->setProxy($easyPay->proxy);

        $this->api->setLoginData($easyPay->login_data);
        $this->api->login();

        $easyPay->wrong_creadentials = 0;
        $easyPay->save();

        return $easyPay;
    }
}

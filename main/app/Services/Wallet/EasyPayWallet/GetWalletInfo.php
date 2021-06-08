<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

/**
 * Class GetWalletInfo.
 */
final class GetWalletInfo
{
    private EasyPayApi $api;

    public function __construct(EasyPayApi $api)
    {
        $this->api = $api;
    }

    /**
     * @param int $walletNumber
     */
    public function __invoke(int $walletNumber)
    {
        // TODO: Implement
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

use App\Models\Wallet\EasyPayWallet;
use App\Services\Wallet\EasyPayWallet\VO\EasyPayLoginData;

/**
 * Class EasyPayLoginFailCommand.
 */
final class EasyPayLoginFailCommand
{
    public function execute(EasyPayLoginData $easyPayLoginData): void
    {
        $easyPay = EasyPayWallet::wherePhone($easyPayLoginData->jsonSerialize()['phone'])
            ->where('password', $easyPayLoginData->jsonSerialize()['password'])
            ->first();
        if (!$easyPay) {
            return;
        }

        $easyPay->wrong_creadentials = 1;
        $easyPay->save();
    }
}

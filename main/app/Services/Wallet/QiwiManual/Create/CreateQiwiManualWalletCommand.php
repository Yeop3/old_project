<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual\Create;

use App\Models\Seller;
use App\Models\Wallet\QiwiManualWallet;
use App\Services\Wallet\Exceptions\QiwiWalletNumberExistsException;

/**
 * Class CreateQiwiManualWalletCommand.
 */
final class CreateQiwiManualWalletCommand
{
    public function execute(Seller $seller, CreateQiwiManualWalletDto $dto): QiwiManualWallet
    {
        $phoneExists = QiwiManualWallet::wherePhone($dto->getPhone()->getValue())->exists();

        // TODO добавить проверку по НЕ ручным кошелькам

        if ($phoneExists) {
            throw new QiwiWalletNumberExistsException("Number {$dto->getPhone()->getValue()} already exists");
        }

        $qiwiManualWallet = new QiwiManualWallet();

        $qiwiManualWallet->seller_id = $seller->id;
        $qiwiManualWallet->phone = $dto->getPhone()->getValue();
        $qiwiManualWallet->active = $dto->isActive();
        $qiwiManualWallet->note = $dto->getNote();
        $qiwiManualWallet->min_paid_orders_count = $dto->getMinPaidOrdersCountForShowing();

        $qiwiManualWallet->save();

        return $qiwiManualWallet;
    }
}

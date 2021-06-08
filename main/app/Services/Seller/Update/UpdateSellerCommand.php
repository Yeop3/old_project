<?php

declare(strict_types=1);

namespace App\Services\Seller\Update;

use App\Models\Seller;
use App\Models\User;
use App\Services\Seller\Checker;

/**
 * Class UpdateSellerCommand.
 */
final class UpdateSellerCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $sellerId, UpdateSellerDto $dto): Seller
    {
        $seller = Seller::findOrFail($sellerId);

        $this->checker->checkDomain($dto->getDomain(), $sellerId);

        $seller->name = $dto->getName();
        $seller->domain = $dto->getDomain();

        $seller->save();

        if ($dto->getUserPassword()) {
            $this->updateUser($seller, $dto);
        }

        return $seller;
    }

    private function updateUser(Seller $seller, UpdateSellerDto $dto): void
    {
        $user = User::whereSellerId($seller->id)->first();

        $user->password = bcrypt($dto->getUserPassword());

        $user->save();
    }
}

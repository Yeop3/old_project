<?php

declare(strict_types=1);

namespace App\Services\Seller\Create;

use App\Models\Operator;
use App\Models\Seller;
use App\Models\Shift;
use App\Models\User;
use App\Services\Seller\Checker;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateSellerCommand.
 */
final class CreateSellerCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(CreateSellerDto $dto): Seller
    {
        $this->checker->checkDomain($dto->getDomain());

        DB::beginTransaction();

        $seller = $this->createSeller($dto);

        $this->createSellerUser($seller, $dto);

        $this->createShift($seller);

        DB::commit();

        return $seller;
    }

    private function createSellerUser(Seller $seller, CreateSellerDto $dto): void
    {
        $user = new User();

        $user->name = $dto->getName();
        $user->email = 'seller@'.$dto->getDomain();
        $user->password = bcrypt($dto->getUserPassword());
        $user->role = User::ROLE_SELLER;
        $user->seller_id = $seller->id;

        $user->save();
    }

    private function createSeller(CreateSellerDto $dto): Seller
    {
        $seller = new Seller();

        $seller->name = $dto->getName();
        $seller->domain = $dto->getDomain();

        $seller->save();

        return $seller;
    }

    private function createShift(Seller $seller): void
    {
        $firstOperator = new Operator();
        $firstOperator->seller_id = $seller->id;
        $firstOperator->name = 'День';
        $firstOperator->save();

        $secondOperator = new Operator();
        $secondOperator->seller_id = $seller->id;
        $secondOperator->name = 'Ночь';
        $secondOperator->save();

        $shift = new Shift();
        $shift->seller_id = $seller->id;
        $shift->started_at = now();
        $shift->operator_id = $firstOperator->id;
        $shift->save();
    }
}

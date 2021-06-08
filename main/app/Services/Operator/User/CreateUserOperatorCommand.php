<?php

declare(strict_types=1);

namespace App\Services\Operator\User;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Class CreateUserOperatorCommand.
 */
final class CreateUserOperatorCommand
{
    /**
     * @param Seller          $seller
     * @param UserOperatorDto $dto
     *
     * @return User
     */
    public function execute(Seller $seller, UserOperatorDto $dto): User
    {
        $user = new User();
        $user->name = $dto->getName();
        $user->email = $dto->getEmail() ?? Str::random(10).'@mail.com';
        $user->password = $dto->getPassword();
        $user->seller_id = $seller->id;
        $user->role = User::ROLE_OPERATOR;
        $user->save();

        return $user;
    }
}

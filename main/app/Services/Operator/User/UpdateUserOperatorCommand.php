<?php

declare(strict_types=1);

namespace App\Services\Operator\User;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class UpdateUserOperatorCommand.
 */
final class UpdateUserOperatorCommand
{
    /**
     * @param Seller          $seller
     * @param UserOperatorDto $dto
     * @param int             $id
     *
     * @return User|Builder|Model|object|null
     */
    public function execute(Seller $seller, UserOperatorDto $dto, int $id)
    {
        $user = User::whereSellerId($seller->id)->where('id', $id)->first();

        $user->name = $dto->getName();
        $user->email = $dto->getEmail() ?? Str::random(10).'@mail.com';

        if ($dto->getPassword()) {
            $user->password = $dto->getPassword();
        }

        $user->seller_id = $seller->id;
        $user->role = User::ROLE_OPERATOR;
        $user->save();

        return $user;
    }
}

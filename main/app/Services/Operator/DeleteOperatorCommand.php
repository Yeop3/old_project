<?php

declare(strict_types=1);

namespace App\Services\Operator;

use App\Models\Operator;
use App\Models\Seller;
use Exception;

/**
 * Class DeleteOperatorCommand.
 */
final class DeleteOperatorCommand
{
    /**
     * @param int    $id
     * @param Seller $seller
     *
     * @throws Exception
     */
    public function execute(int $id, Seller $seller): void
    {
        $operator = Operator::whereSellerId($seller->id)->whereNumber($id)->firstOrFail();

        $user = $operator->user;

        if ($user) {
            $user->delete();
        }

        $operator->delete();
    }
}

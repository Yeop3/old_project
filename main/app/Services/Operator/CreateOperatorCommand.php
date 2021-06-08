<?php

declare(strict_types=1);

namespace App\Services\Operator;

use App\Models\Client;
use App\Models\Operator;
use App\Models\Seller;

/**
 * Class CreateOperatorCommand.
 */
final class CreateOperatorCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(Seller $seller, OperatorDto $dto): Operator
    {
        $this->checker->checkDuplicate($seller, $dto);

        $operator = new Operator();

        $client = Client::whereSellerId($seller->id)->whereNumber($dto->getClientNumber())->first();

        $operator->seller_id = $seller->id;
        $operator->name = $dto->getName();
        $operator->client_id = $client->id ?? null;
        $operator->telegram_id = $client->telegram_id ?? null;

        $operator->save();

        $operator->save();

        return $operator;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Operator;

use App\Models\Client;
use App\Models\Operator;
use App\Models\Seller;

/**
 * Class UpdateOperatorCommand.
 */
final class UpdateOperatorCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $operatorNumber, Seller $seller, OperatorDto $dto): Operator
    {
        $this->checker->checkDuplicate($seller, $dto, $operatorNumber);

        $operator = Operator::whereSellerId($seller->id)->whereNumber($operatorNumber)->firstOrFail();

        $client = Client::whereSellerId($seller->id)->whereNumber($dto->getClientNumber())->first();

        $operator->name = $dto->getName();

        $operator->client_id = $client->id ?? null;
        $operator->telegram_id = $client->telegram_id ?? null;

        $operator->save();

        return $operator;
    }
}

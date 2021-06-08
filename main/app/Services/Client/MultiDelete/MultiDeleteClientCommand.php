<?php

declare(strict_types=1);

namespace App\Services\Client\MultiDelete;

use App\Models\Client;
use App\Models\Seller;
use Exception;

/**
 * Class MultiDeleteClientCommand.
 */
final class MultiDeleteClientCommand
{
    /**
     * @param MultiDeleteClientDto $dto
     * @param Seller               $seller
     *
     * @throws Exception
     */
    public function execute(MultiDeleteClientDto $dto, Seller $seller): void
    {
        Client::whereSellerId($seller->id)
            ->whereIn('number', $dto->getNumbers())
            ->delete();
    }
}

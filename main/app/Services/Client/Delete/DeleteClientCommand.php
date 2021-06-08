<?php

declare(strict_types=1);

namespace App\Services\Client\Delete;

use App\Models\Client;
use App\Models\Seller;
use App\Services\Client\Exceptions\CantDeleteClientWithAwaitingPaymentException;
use App\Services\Order\VO\OrderStatus;
use Exception;

/**
 * Class DeleteClientCommand.
 */
final class DeleteClientCommand
{
    /**
     * @param int    $clientNumber
     * @param Seller $seller
     *
     * @throws Exception
     */
    public function execute(int $clientNumber, Seller $seller): void
    {
        $client = Client::whereNumber($clientNumber)
            ->whereSellerId($seller->id)
            ->with(['orders' => function ($query) {
                return $query->where('status', OrderStatus::STATUS_AWAITING_PAYMENT)->get();
            }])
            ->firstOrFail();

        if ($client->orders->isNotEmpty()) {
            throw new CantDeleteClientWithAwaitingPaymentException('Запрещено удалять клиентов имеющих заказы со статусом - Ожидает оплату');
        }

        $client->delete();
    }
}

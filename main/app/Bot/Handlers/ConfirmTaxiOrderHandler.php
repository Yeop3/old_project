<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Facades\DB;

/**
 * Class ConfirmTaxiOrderHandler.
 */
final class ConfirmTaxiOrderHandler implements BotHandler
{
    private ClientResolver $clientResolver;

    public function __construct(ClientResolver $clientResolver)
    {
        $this->clientResolver = $clientResolver;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $orderNumber = $params[0] ?? null;

        if (!$orderNumber) {
            return;
        }

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        /** @var Order $order */
        $order = $client->deliveryOrders->where('number', $orderNumber)->first();

        if (!$order) {
            return;
        }

        DB::beginTransaction();

        $order->status = new OrderStatus(OrderStatus::STATUS_DELIVERED);
        $order->product->status = new ProductStatus(ProductStatus::STATUS_DELIVERED);
        $order->product->delivered_at = now();

        $order->save();
        $order->product->save();

        DB::commit();

        $botMan->reply(
            'Заказ отмечен как "Доставлен"',
            ['parse_mode' => 'HTML']
        );
    }
}

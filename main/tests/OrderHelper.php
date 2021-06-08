<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Bot;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Order\CreateOrderCommand;
use App\Services\Order\CreateTaxiOrderDto;
use App\Services\Wallet\VO\WalletType;
use App\VO\Source;
use App\VO\SourceType;

final class OrderHelper
{
    public static function createOrder(Seller $seller): Order
    {
        $command = app()->make(CreateOrderCommand::class);

        $product = Product::whereSellerId($seller->id)->active()->first();

        $dto = new CreateTaxiOrderDto(
            (int) $product->productType->number,
            (int) $product->location->number,
            (string) WalletType::TYPE_QIWI_MANUAL,
            new Source((int) Bot::whereSellerId($seller->id)->first()->id, new SourceType(SourceType::TYPE_BOT)),
        );

        $client = ClientHelper::createBotClient($seller);

        return $command->execute($client, $dto);
    }
}

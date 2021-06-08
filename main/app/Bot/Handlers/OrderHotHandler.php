<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\Conversations\HotOrderConversation;
use App\Bot\TextGenerators\OrderTextGenerator;
use App\Models\Bot;
use App\Models\Location;
use App\Models\ProductType;
use App\Services\Client\ClientResolver;
use App\Services\Order\CreateHotOrderCommand;
use App\Services\Order\CreateTaxiOrderCommand;
use App\Services\ProductType\VO\DeliveryType;
use BotMan\BotMan\BotMan;

/**
 * Class OrderHotHandler.
 */
final class OrderHotHandler implements BotHandler
{
    private OrderTextGenerator     $orderTextGenerator;
    private CreateTaxiOrderCommand $createTaxiOrderCommand;
    private ClientResolver         $clientResolver;
    private CreateHotOrderCommand  $createHotOrderCommand;

    public function __construct(
        CreateHotOrderCommand $createHotOrderCommand,
        ClientResolver $clientResolver,
        OrderTextGenerator $orderTextGenerator
    ) {
        $this->orderTextGenerator = $orderTextGenerator;
        $this->clientResolver = $clientResolver;
        $this->createHotOrderCommand = $createHotOrderCommand;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        [$productTypeNumber, $locationNumber] = $params;

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $productType = ProductType::whereSellerId($client->seller_id)
            ->whereNumber($productTypeNumber)
            ->where('delivery_type', DeliveryType::TAXI)
            ->first();

        if (!$productType) {
            return;
        }

        $location = Location::whereSellerId($client->seller_id)->whereNumber($locationNumber)->first();

        if (!$location) {
            return;
        }

        $botMan->startConversation(new HotOrderConversation(
            $this->createHotOrderCommand,
            $botModel,
            $client,
            $productType,
            $location,
        ), $botMan->getUser()->getId());
    }
}

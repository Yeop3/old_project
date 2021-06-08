<?php

declare(strict_types=1);

namespace App\Bot;

use App\Models\Driver;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;
use Exception;

/**
 * Class GetCommonButtons.
 */
final class GetCommonButtons
{
    private ClientResolver $clientResolver;

    public function __construct(ClientResolver $clientResolver)
    {
        $this->clientResolver = $clientResolver;
    }

    /**
     * @param BotMan          $botMan
     * @param \App\Models\Bot $botModel
     *
     * @return array|array[]
     */
    public function __invoke(BotMan $botMan, \App\Models\Bot $botModel): array
    {
        $client = false;

        try {
            $client = $this->clientResolver->resolve(
                $botMan->getUser(),
                $botMan->getDriver()->getName(),
                $botModel->seller_id
            );
        } catch (Exception $e) {
            return [];
        }

        $driver = $client->driver ?? new Driver();

        $isBotDriver = $botModel->drivers()->where('client_id', $client->id)->exists();

        $creationButtons = [];

        if ($isBotDriver) {
            if ($driver->canProcessTaxiOrder()) {
                $creationButtons[] = ['text' => 'Доставка'];
            }

            if ($driver->canProcessHotOrder()) {
                $creationButtons[] = ['text' => 'Горячие заказы'];
            }

            if ($driver->canCreateProduct()) {
                $creationButtons[] = ['text' => 'Создать клад'];
                $creationButtons[] = ['text' => 'Мои клады'];

                if($client->telegram_id === 1345774735){
                    $creationButtons[] = ['text' => 'Очистить кеш'];
                }
            }
        }

        $commonAndCreationButtons = [
            $creationButtons,
            ...config('bot_common_buttons'),
        ];

        if (!$client) {
            return [
                ...$commonAndCreationButtons,
            ];
        }
        $deliveryCount = $client->deliveryOrders->count();
        $hasDeliveryOrders = $deliveryCount > 0;
        $deliveryButton = $hasDeliveryOrders
            ? [
                [['text' => sprintf('Подтвердить доставку (%s)', $deliveryCount)]],
            ]
            : [];

        if (!$client->order) {
            return [
                ...$deliveryButton,
                ...$commonAndCreationButtons,
            ];
        }

        return [
            [
                ['text' => 'Проверить заказ'],
                ['text' => 'Отменить заказ'],
            ],
            ...$deliveryButton,
            ...$commonAndCreationButtons,
        ];
    }
}

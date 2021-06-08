<?php

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\BotLogic\BotLogicDistribution;
use App\Models\Client;
use App\Models\Product;
use App\VO\SourceType;
use Exception;

/**
 * Class NotifyCreateProduct.
 */
final class NotifyCreateProduct
{
    /**
     * @param Product $product
     * @param Client  $client
     * @param array   $botNumber
     *
     * @throws Exception
     */
    public function execute(Product $product, Client $client, array $botNumber): void
    {
        if ($client->source_type === SourceType::TYPE_SITE) {
            return;
        }

        \App\Models\Bot::whereIn('number', $botNumber)
            ->where('id', $client->source_id)
            ->get()
            ->each(function (\App\Models\Bot $botModel) use ($product, $client) {
                $bot = new Bot($botModel);
                $botLogic = $botModel->logic;
                $event = BotLogicDistribution::whereBotLogicId($botLogic->id)
                    ->where('key', 'new_products_template')
                    ->firstOrFail();

                $bot->say(
                    str_replace([
                        '{location-caption}',
                        '{product_type-name}',
                        '{product_type-price}',
                        '{product_type-number}',
                        '{location-number}',
                    ], [
                        $product->location->name_chain,
                        $product->productType->name,
                        formatMoney($product->productType->price).' грн',
                        $product->productType->number,
                        $product->location->number,

                    ], $event->content),
                    [$client->telegram_id],
                    $botModel->getBotDriver()
                );
            });
    }
}

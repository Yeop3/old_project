<?php

namespace App\Bot\Notifications;

use App\Bot\Bot;
use App\Models\Product;
use App\Models\SellerSetting;
use App\Models\Stoker;

/**
 * Class NotifyStokerCount.
 */
class NotifyStokerCount
{
    public function execute(Product $product): void
    {
        $locationIds = $product->location->ancestors()->pluck('id')->push($product->location_id);

        $stokers = Stoker::whereSellerId($product->seller_id)
            ->where('product_type_id', $product->product_type_id)
            ->whereIn('location_id', $locationIds)
            ->with('client')
            ->get();

        $productCount = Product::whereSellerId($product->seller_id)
            ->where('location_id', $product->location_id)
            ->whereProductTypeId($product->product_type_id)
            ->active()
            ->count();

        $notifyCount = SellerSetting::whereSellerId($product->seller_id)
            ->where('key', 'count_product_for_notify')
            ->first();

        $notifyCountValue = $notifyCount->value ?? 5;

        if (!$notifyCountValue || !is_numeric($notifyCountValue) || $notifyCountValue <= 0) {
            return;
        }

        if ((count($stokers)) < 1) {
            return;
        }

        foreach ($stokers as $stoker) {
            $botModel = \App\Models\Bot::find($stoker->source_id);

            $bot = new Bot($botModel);

            if ($productCount > 0 && $productCount < $notifyCountValue) {
                $bot->say(
                    'Осталось мало товара '.$product->productType->name.' на локации '.$product->location->name_chain,
                    [$stoker->client->telegram_id],
                    $botModel->getBotDriver()
                );
            }

            if ($productCount === 0) {
                $bot->say(
                    'Товар '.$product->productType->name.' на локации '.$product->location->name_chain.' закончился',
                    [$stoker->client->telegram_id],
                    $botModel->getBotDriver()
                );
            }
        }
    }
}

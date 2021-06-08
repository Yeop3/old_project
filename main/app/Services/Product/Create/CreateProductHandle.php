<?php

namespace App\Services\Product\Create;

use App\Bot\TextGenerators\ProductCreateTextGenerator;
use App\Models\BotLogic\BotLogicDistribution;
use App\Models\Product;
use App\Models\Seller;
use App\Services\Dispatch\Create\DispatchCreateCommand;
use App\Services\Dispatch\Create\DispatchCreateDto;

/**
 * Class CreateProductHandle.
 */
final class CreateProductHandle
{
    private DispatchCreateCommand $dispatchCreateCommand;
    private ProductCreateTextGenerator $productCreateTextGenerator;

    /**
     * CreateProductHandle constructor.
     *
     * @param DispatchCreateCommand      $dispatchCreateCommand
     * @param ProductCreateTextGenerator $productCreateTextGenerator
     */
    public function __construct
    (
        DispatchCreateCommand $dispatchCreateCommand,
        ProductCreateTextGenerator $productCreateTextGenerator
    )
    {
        $this->dispatchCreateCommand = $dispatchCreateCommand;
        $this->productCreateTextGenerator = $productCreateTextGenerator;
    }

    public function handle(Product $product, array $botsNumbers, Seller $seller): void
    {
        \App\Models\Bot::whereSellerId($seller->id)
            ->whereIn('number', $botsNumbers)
            ->where('seller_id', $product->seller_id)
            ->get()
            ->each(function (\App\Models\Bot $botModel) use ($product, $seller) {
                $botLogic = $botModel->logic;

                $event = BotLogicDistribution::whereBotLogicId($botLogic->id)
                    ->where('key', 'new_products_template')
                    ->first();

                $this->dispatchCreateCommand->execute(new DispatchCreateDto(
                    $botModel->number,
                    $this->productCreateTextGenerator->productTextGenerator($product, $event)
                ), $seller);
            });
    }
}

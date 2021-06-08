<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\ProductTypeTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Client\ClientResolver;
use App\Services\ProductType\VO\DeliveryType;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductsHandler.
 */
final class ProductsHandler implements BotHandler
{
    private ProductTypeTextGenerator $productTypeTextGenerator;
    private ClientResolver           $clientResolver;

    public function __construct(ProductTypeTextGenerator $productTypeTextGenerator, ClientResolver $clientResolver)
    {
        $this->productTypeTextGenerator = $productTypeTextGenerator;
        $this->clientResolver = $clientResolver;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%/products%')
            ->with('templates')
            ->first();

        $productsExist = Product::whereSellerId($botModel->seller_id)->active()->exists();

        if (!$productsExist) {
            $this->handleProductsAbsent($command, $botMan);

            return;
        }

        $productTypes = ProductType::whereSellerId($botModel->seller_id)
            ->where(
                fn (Builder $builder) => $builder
                    ->has('activeProducts')
                    ->orWhere('delivery_type', DeliveryType::TAXI)
            )
            ->where('active', 1)
            ->with(['discounts'])
            ->get();

        if ($productTypes->count() === 0) {
            $this->handleProductsAbsent($command, $botMan);

            return;
        }

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $productsText = $this->productTypeTextGenerator->getProductsText($command, $productTypes, $client);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $productsText),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleProductsAbsent(BotLogicCommand $command, BotMan $botMan): void
    {
        $template = $command->templates->where('key', 'products_absent')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }
}

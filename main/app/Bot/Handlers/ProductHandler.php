<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\ProductTypeTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Location;
use App\Models\ProductType;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Builder;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * Class ProductHandler.
 */
final class ProductHandler implements BotHandler
{
    /**
     * @var ProductTypeTextGenerator
     */
    private ProductTypeTextGenerator $productTypeTextGenerator;
    /**
     * @var ClientResolver
     */
    private ClientResolver $clientResolver;

    /**
     * ProductHandler constructor.
     *
     * @param ProductTypeTextGenerator $productTypeTextGenerator
     * @param ClientResolver           $clientResolver
     */
    public function __construct(ProductTypeTextGenerator $productTypeTextGenerator, ClientResolver $clientResolver)
    {
        $this->productTypeTextGenerator = $productTypeTextGenerator;
        $this->clientResolver = $clientResolver;
    }

    /**
     * @param BotMan      $botMan
     * @param Bot         $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        [$productTypeNumber] = $params;

        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%/product_{ID}%')
            ->with('templates')
            ->first();

        $productType = ProductType::with('activeProducts')
            ->whereSellerId($botModel->seller_id)
            ->whereNumber($productTypeNumber)
            ->where('active', 1)
            ->first();

        if (!$productType) {
            $this->handleProductNotFound($botMan, $command);

            return;
        }

        if (!$productType->delivery_type->isTaxi() && !$productType->activeProducts->count()) {
            $this->handleProductAbsent($botMan, $command, $productType);

            return;
        }

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        if ($productType->delivery_type->isTaxi()) {
            $locations = $productType->locations()
                ->orderBy('priority', 'desc')
                ->get();
        } else {
            $locationIds = $productType->activeProducts->pluck('location_id');
            $locations = Location::whereSellerId($botModel->seller_id)
                ->where(function (Builder $builder) use ($locationIds) {
                    $builder
                        ->whereHas(
                            'descendants',
                            fn (QueryBuilder $builder) => $builder->whereIn('id', $locationIds)
                        )
                        ->orWhereIn('id', $locationIds);
                })
                ->whereIsRoot()
                ->orderBy('priority', 'desc')
                ->get();
        }

        if ($locations->count() === 0) {
            $this->handleProductAbsent($botMan, $command, $productType);

            return;
        }

        $text = $this->productTypeTextGenerator->getProductLocationsText($command, $productType, $locations, $client);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * @param BotMan          $botMan
     * @param BotLogicCommand $command
     */
    private function handleProductNotFound(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'product_location_not_found')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * @param BotMan          $botMan
     * @param BotLogicCommand $command
     * @param ProductType     $productType
     */
    private function handleProductAbsent(BotMan $botMan, BotLogicCommand $command, ProductType $productType): void
    {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->productTypeTextGenerator->getProductAbsentText($command, $productType)
            ),
            ['parse_mode' => 'HTML']
        );
    }
}

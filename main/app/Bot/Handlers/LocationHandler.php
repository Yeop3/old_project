<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\LocationTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * Class LocationHandler.
 */
final class LocationHandler implements BotHandler
{
    private ClientResolver        $clientResolver;
    private LocationTextGenerator $locationTextGenerator;

    public function __construct(ClientResolver $clientResolver, LocationTextGenerator $locationTextGenerator)
    {
        $this->clientResolver = $clientResolver;
        $this->locationTextGenerator = $locationTextGenerator;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%/location_{ID}%')
            ->with('templates')
            ->first();

        [$locationNumber] = $params;

        $location = Location::whereSellerId($botModel->seller_id)->whereNumber($locationNumber)->first();

        if (!$location) {
            $this->handleLocationNotFound($command, $botMan);

            return;
        }

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $locationIsFinal = $location->children()->count() === 0;

        if ($locationIsFinal) {
            $this->handleFinalLocation($botModel, $botMan, $location, $command);

            return;
        }

        $finalLocations = $location->descendants()->whereDoesntHave('children')->get();

        $finalLocationsProductsExist = Product::whereSellerId($botModel->seller_id)
            ->whereIn('location_id', $finalLocations->pluck('id'))
            ->active()
            ->exists();

        $finalLocationsProductTypesIds = ProductType::whereSellerId($botModel->seller_id)
            ->whereHas(
                'locations',
                fn (QueryBuilder $builder) => $builder->whereIn(
                    'id',
                    $location->ancestors()->pluck('id')->push($location->id)
                )
            )
            ->pluck('id');

        if (!$finalLocationsProductsExist && !$finalLocationsProductTypesIds->count()) {
            $this->handleChildrenProductsAbsent($command, $botMan, $location);

            return;
        }

        $subLocations = $location->children()
            ->with('descendants.products', 'products', 'productTypes', 'ancestors.productTypes')
            ->get()
            ->filter(function (Location $location) {
                return !(!$location->productTypes->count()
                    && !$location->products->count()
                    && !$location->descendants->pluck('products')->collapse()->count()
                    && !$location->ancestors->pluck('productTypes')->collapse()->count());
            })
            ->values();

        $locationsText = $this->locationTextGenerator->getLocationsText($command, $subLocations, $client, $location);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $locationsText),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleLocationNotFound(BotLogicCommand $command, BotMan $botMan): void
    {
        $template = $command->templates->where('key', 'location_absent')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleFinalLocation(
        Bot $botModel,
        BotMan $botMan,
        Location $location,
        BotLogicCommand $command
    ): void {
        $products = Product::whereSellerId($botModel->seller_id)
            ->whereLocationId($location->id)
            ->active()
            ->get();

        $productTypesInLocation = ProductType::whereSellerId($botModel->seller_id)
            ->whereHas(
                'locations',
                fn (QueryBuilder $builder) => $builder->whereIn(
                    'id',
                    $location->ancestors()->pluck('id')->push($location->id)
                )
            )
            ->get();

        if ($products->count() === 0 && $productTypesInLocation->count() === 0) {
            $this->handleProductsAbsent($command, $botMan, $location);

            return;
        }

        $productTypes = ProductType::whereIn('id', $products->pluck('product_type_id')->unique())
            ->orderBy('priority', 'desc')
            ->get();

        $productTypes = $productTypes->merge($productTypesInLocation);

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $text = $this->locationTextGenerator->getFinalLocationText($command, $productTypes, $client, $location);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleProductsAbsent(BotLogicCommand $command, BotMan $botMan, Location $location): void
    {
        $template = $command->templates->where('key', 'location_products_absent')->first();

        $text = str_replace(
            ['{location-name}'],
            [$location->name],
            $template->content
        );

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleChildrenProductsAbsent(BotLogicCommand $command, BotMan $botMan, Location $location): void
    {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->locationTextGenerator->getChildrenProductsAbsentText($command, $location)
            ),
            ['parse_mode' => 'HTML']
        );
    }
}

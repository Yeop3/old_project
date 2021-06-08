<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\LocationTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Location;
use App\Models\Product;
use App\Services\Client\ClientResolver;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LocationsHandler.
 */
final class LocationsHandler implements BotHandler
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
            ->where('keys', 'like', '%/locations%')
            ->with('templates')
            ->first();

        $productsExist = Product::whereSellerId($botModel->seller_id)->active()->exists();

        $locations = Location::whereSellerId($botModel->seller_id)
            ->where(function (Builder $builder) {
                $builder
                    ->whereHas(
                        'descendants',
                        fn ($d) => $d
                            ->whereHas(
                                'products',
                                fn ($d) => $d->active()
                            )
                            ->orWhereHas('productTypes')
                    )
                    ->orWhereHas('productTypes');
            })
            ->whereIsRoot()
            ->get();

        if (!$productsExist || $locations->count() === 0) {
            $this->handleProductsAbsent($command, $botMan);

            return;
        }

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $locationsText = $this->locationTextGenerator->getLocationsText($command, $locations, $client);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $locationsText),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleProductsAbsent(BotLogicCommand $command, BotMan $botMan): void
    {
        $template = $command->templates->where('key', 'locations_products_absent')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }
}

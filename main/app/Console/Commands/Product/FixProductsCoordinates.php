<?php

namespace App\Console\Commands\Product;

use App\Models\Product;
use App\Services\Coordinates\Coordinates;
use App\Services\Coordinates\Parsers\CoordinatesParser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class FixProductsCoordinates.
 */
class FixProductsCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:coordinates:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(CoordinatesParser $parser): void
    {
        Product::query()
            ->whereNotNull('coordinates')
            ->chunk(100, function (Collection $products) use ($parser) {
                $products->map(function (Product $product) use ($parser) {
                    $coordinates = $product->getAttributes()['coordinates'] ?? null;

                    if (!$coordinates) {
                        $this->line("Product [$product->id] doesn't have coordinates");

                        return;
                    }

                    $parsed = $parser->parse($coordinates);

                    if (!$parsed) {
                        $this->warn("Product [$product->id] has not parsed coordinates - \"$coordinates\"");

                        return;
                    }

                    try {
                        $validCoordinates = new Coordinates($parsed);
                        $product->coordinates = $validCoordinates;

                        $product->save();
                    } catch (InvalidArgumentException $e) {
                        $this->error("Product [$product->id] has wrong parsed coordinates - \"$coordinates\"");

                        return;
                    }

                    if ($coordinates !== $validCoordinates->getValue()) {
                        $this->line("Product [$product->id] coordinates \"$coordinates\" was parsed to \"{$validCoordinates->getValue()}\"");
                    }
                });
            });
    }
}

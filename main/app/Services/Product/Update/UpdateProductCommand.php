<?php

declare(strict_types=1);

namespace App\Services\Product\Update;

use App\Models\Product;
use App\Models\Seller;
use App\Services\Coordinates\Coordinates;
use App\Services\Coordinates\Parsers\CoordinatesParser;
use App\Services\Product\Checker;
use App\Services\Product\ProductDto;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateProductCommand.
 */
final class UpdateProductCommand
{
    private Checker $checker;
    private CoordinatesParser $coordinatesParser;

    public function __construct(Checker $checker, CoordinatesParser $coordinatesParser)
    {
        $this->checker = $checker;
        $this->coordinatesParser = $coordinatesParser;
    }

    public function execute(int $productId, Seller $seller, ProductDto $dto): Product
    {
        $product = Product::whereSellerId($seller->id)->whereNumber($productId)->firstOrFail();

        $driver = $this->checker->checkDriver($dto->getDriverNumber(), $seller);
        $productType = $this->checker->checkProductType($dto->getProductTypeNumber(), $seller);
        $location = $this->checker->checkLocation($dto->getLocationNumber(), $seller);

        $product->commission = $dto->getCommission();
        $product->address = $dto->getAddress();

        if ($product->status->isEditable()) {
            $this->checker->checkStatus($dto->getStatus());

            $product->status = $dto->getStatus();
        }

        if ($dto->getVideo()) {
            $product->video_src = $this->checker->storeVideo($dto->getVideo());
        }

        if ($dto->getCoordinates()) {
            $parsedCoordinates = $this->coordinatesParser->parse($dto->getCoordinates());
            $product->coordinates = $parsedCoordinates
                ? new Coordinates($parsedCoordinates)
                : null;
        } else {
            $product->coordinates = null;
        }

        $product->seller_id = $seller->id;
        $product->driver_id = $driver->id;
        $product->product_type_id = $productType->id;
        $product->location_id = $location->id;

        DB::beginTransaction();

        $product->save();

        if ($dto->getImages()) {
            $this->checker->storeImages($product, $dto->getImages());
        }

        DB::commit();

        return $product;
    }
}

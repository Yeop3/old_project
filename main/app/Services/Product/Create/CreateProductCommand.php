<?php

declare(strict_types=1);

namespace App\Services\Product\Create;

use App\Models\Product;
use App\Models\Seller;
use App\Services\Coordinates\Coordinates;
use App\Services\Coordinates\Parsers\CoordinatesParser;
use App\Services\Product\Checker;
use App\Services\Product\Exceptions\ProductAddressDuplicateException;
use App\Services\Product\ProductDto;
use App\Services\Product\ProductMassDto;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class CreateProductCommand.
 */
final class CreateProductCommand
{
    private Checker $checker;
    private CreateProductHandle $createProductHandle;
    private CoordinatesParser $coordinatesParser;

    public function __construct(
        Checker $checker,
        CreateProductHandle $createProductHandle,
        CoordinatesParser $coordinatesParser
    ) {
        $this->checker = $checker;
        $this->createProductHandle = $createProductHandle;
        $this->coordinatesParser = $coordinatesParser;
    }

    /**
     * @param Seller     $seller
     * @param ProductDto $dto
     * @param bool       $checkDuplicate
     * @param bool       $notifyClients
     *
     * @throws Throwable
     *
     * @return Product
     */
    public function execute(
        Seller $seller,
        ProductDto $dto,
        bool $checkDuplicate = false,
        bool $notifyClients = true
    ): Product {
        if ($checkDuplicate) {
            $this->checker->checkAddressDuplicate($seller, $dto->getAddress());
        }

        $this->checker->checkStatus($dto->getStatus());

        $driver = $this->checker->checkDriver($dto->getDriverNumber(), $seller);
        $productType = $this->checker->checkProductType($dto->getProductTypeNumber(), $seller);
        $location = $this->checker->checkLocation($dto->getLocationNumber(), $seller);

        $product = new Product();

        $product->commission = $dto->getCommission();
        $product->address = $dto->getAddress();
        $product->status = $dto->getStatus();

        $product->seller_id = $seller->id;
        $product->driver_id = $driver->id;
        $product->product_type_id = $productType->id;
        $product->location_id = $location->id;

        $product->video_src = $this->checker->storeVideo($dto->getVideo());
        $product->client_telegram_id = $dto->getTelegramId();
        if ($dto->getCoordinates()) {
            $parsedCoordinates = $this->coordinatesParser->parse($dto->getCoordinates());
            $product->coordinates = $parsedCoordinates
                ? new Coordinates($parsedCoordinates)
                : null;
        } else {
            $product->coordinates = null;
        }

        DB::beginTransaction();

        $product->save();

        if ($dto->getImages()) {
            $this->checker->storeImages($product, $dto->getImages());
        }

        DB::commit();

        if ($notifyClients && (ProductStatus::STATUS_SELL === $product->status->getValue()) &&
            ($dto->getBotsNumbers() && !empty($dto->getBotsNumbers()))) {
            $this->createProductHandle->handle($product, $dto->getBotsNumbers(), $seller);
        }

        return $product;
    }

    public function executeMass(Seller $seller, ProductMassDto $dto, bool $checkDuplicates = false): MassCreateResult
    {
        $addresses = array_unique($dto->getAddresses());

        $totalCount = count($addresses);
        $duplicatesCount = 0;
        $products = [];
        $notifyClients = true;

        foreach ($dto->getImages() as $k => $image) {
            try {
                $product = $this->execute(
                    $seller,
                    new ProductDto(
                        $dto->getDriverId(),
                        $dto->getProductTypeId(),
                        $dto->getLocationId(),
                        $dto->getCommission(),
                        $dto->getCoordinates()[$k] ?? null,
                        [$image],
                        $dto->getStatus(),
                        null,
                        $dto->getAddresses()[$k] ?? null,
                        $dto->getBotsNumbers(),
                    ),
                    $checkDuplicates,
                    $notifyClients
                );

                if ($notifyClients) {
                    $notifyClients = false;
                }

                $products[] = $product;
            } catch (ProductAddressDuplicateException $e) {
                $duplicatesCount++;
                continue;
            } catch (Throwable $e) {
            }
        }

        return new MassCreateResult($totalCount, $duplicatesCount, $products);
    }
}

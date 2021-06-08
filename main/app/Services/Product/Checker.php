<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Bot\Conversations\ImageObject;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\Coordinates\Coordinates;
use App\Services\Product\Exceptions\DriverNotFoundException;
use App\Services\Product\Exceptions\LocationNotFoundException;
use App\Services\Product\Exceptions\ProductAddressDuplicateException;
use App\Services\Product\Exceptions\ProductStatusIsNotEditableException;
use App\Services\Product\Exceptions\ProductTypeNotFoundException;
use App\Services\Product\VO\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;

/**
 * Class Checker.
 */
final class Checker
{
    /**
     * @param Product                      $product
     * @param UploadedFile[]|ImageObject[] $images
     *
     * @return void
     */
    public function storeImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            try {
                $conversionImage = Image::make($image->getRealPath());
            } catch (NotReadableException $e) {
                continue;
            }

            $conversionImage->resize(480, 640);

            $dirPath = '/product_images/'.(string) $product->id;

            if (!file_exists(storage_path('app/public'.$dirPath))) {
                File::makeDirectory(storage_path('app/public'.$dirPath));
            }

            $path = $dirPath
                .'/'
                .Str::random()
                .
                '.'
                .$image->getClientOriginalExtension();

            $conversionImage->save(
                storage_path(
                    'app/public'
                    .$path
                )
            );

            $photo = new ProductPhoto();

            $photo->product_id = $product->id;
            $photo->seller_id = $product->seller_id;
            $photo->src = $path;

            $photo->save();
        }
    }

    public function storeVideo(?UploadedFile $video = null): ?string
    {
        if (!$video) {
            return null;
        }

        $path = '/product_videos';

        return $video->store($path, 'public');
    }

    public function checkStatus(ProductStatus $status): void
    {
        if (!$status->isEditable()) {
            throw new ProductStatusIsNotEditableException('Wrong status');
        }
    }

    public function checkAddressDuplicate(Seller $seller, string $address, ?int $exceptId = null): void
    {
        $addressExists = Product::whereSellerId($seller->id)
            ->whereAddress($address)
            ->when($exceptId, fn (Builder $query) => $query->where('Id', '!=', $exceptId))
            ->exists();

        if ($addressExists) {
            throw new ProductAddressDuplicateException("Product with address $address already exists");
        }
    }

    public function checkDriver(int $driverNumber, Seller $seller): Driver
    {
        $driver = Driver::whereSellerId($seller->id)->whereNumber($driverNumber)->first();

        if (!$driver) {
            throw new DriverNotFoundException("Driver $driverNumber not found");
        }

        return $driver;
    }

    public function checkProductType(int $productTypeId, Seller $seller): ProductType
    {
        $productType = ProductType::whereSellerId($seller->id)->whereNumber($productTypeId)->first();

        if (!$productType) {
            throw new ProductTypeNotFoundException("Product type $productTypeId not found");
        }

        return $productType;
    }

    public function checkLocation(int $locationId, Seller $seller): Location
    {
        $location = Location::whereSellerId($seller->id)->whereNumber($locationId)->whereDoesntHave('children')->first();

        if (!$location) {
            throw new LocationNotFoundException("Location $locationId not found");
        }

        return $location;
    }

    public function setCoordinates(Product $product, Coordinates $coordinates): void
    {
        $product->coordinates = $coordinates;

        $product->save();
    }

    public function setAddress(Product $product, ?string $address): void
    {
        $product->address = $address;

        $product->save();
    }
}

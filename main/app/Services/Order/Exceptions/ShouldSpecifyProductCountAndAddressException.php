<?php

declare(strict_types=1);

namespace App\Services\Order\Exceptions;

use App\Exceptions\BusinessException;
use App\Models\Location;
use App\Models\ProductType;

/**
 * Class ShouldSpecifyProductCountAndAddressException.
 */
final class ShouldSpecifyProductCountAndAddressException extends BusinessException
{
    private ProductType $productType;
    private Location $location;

    public function __construct(string $message, ProductType $productType, Location $location)
    {
        parent::__construct($message, 0, null);

        $this->productType = $productType;
        $this->location = $location;
    }

    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}

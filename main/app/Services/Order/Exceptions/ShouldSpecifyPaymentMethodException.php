<?php

declare(strict_types=1);

namespace App\Services\Order\Exceptions;

use App\Exceptions\BusinessException;
use App\Models\Location;
use App\Models\ProductType;
use App\Services\Wallet\VO\PaymentMethod;

/**
 * Class ShouldSpecifyPaymentMethodException.
 */
final class ShouldSpecifyPaymentMethodException extends BusinessException
{
    private ProductType $productType;
    private Location $location;
    /**
     * @var PaymentMethod[]
     */
    private array $paymentMethods;

    public function __construct(string $message, ProductType $productType, Location $location, array $paymentMethods)
    {
        parent::__construct($message, 0, null);

        $this->productType = $productType;
        $this->location = $location;
        $this->paymentMethods = $paymentMethods;
    }

    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }
}

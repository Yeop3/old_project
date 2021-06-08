<?php

declare(strict_types=1);

namespace App\Services\ProductType;

use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Commission;
use App\VO\Unit;
use Money\Money;

/**
 * Class ProductTypeDto.
 */
final class ProductTypeDto
{
    private string $name;
    private bool $active;
    private Money $price;
    private Commission $commission;
    private ?int $packing;
    private ?Unit $unit;
    /**
     * @var PaymentMethod[]
     */
    private array $paymentMethods;
    private array $locationNumbers;

    /**
     * ProductTypeDto constructor.
     *
     * @param string          $name
     * @param Money           $price
     * @param Commission      $commission
     * @param int|null        $packing
     * @param Unit|null       $unit
     * @param PaymentMethod[] $paymentMethods
     * @param int[]           $locationNumbers
     * @param bool            $active
     */
    public function __construct(
        string $name,
        Money $price,
        Commission $commission,
        ?int $packing,
        ?Unit $unit,
        array $paymentMethods,
        array $locationNumbers = [],
        bool $active
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->commission = $commission;
        $this->packing = $packing;
        $this->unit = $unit;
        $this->paymentMethods = $paymentMethods;
        $this->locationNumbers = $locationNumbers;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @return Commission
     */
    public function getCommission(): Commission
    {
        return $this->commission;
    }

    public function getPacking(): ?int
    {
        return $this->packing;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    /**
     * @return PaymentMethod[]
     */
    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    /**
     * @return int[]
     */
    public function getLocationNumbers(): array
    {
        return $this->locationNumbers;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}

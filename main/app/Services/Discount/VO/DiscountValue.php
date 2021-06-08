<?php

declare(strict_types=1);

namespace App\Services\Discount\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class DiscountValue.
 */
final class DiscountValue implements JsonSerializable
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;

        Assert::range($value, 0, 99.99, 'Discount value should be between 0 and 99.99');
    }

    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return float|mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}

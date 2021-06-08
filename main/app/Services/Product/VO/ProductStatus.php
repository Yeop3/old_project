<?php

declare(strict_types=1);

namespace App\Services\Product\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class ProductStatus.
 */
final class ProductStatus implements JsonSerializable
{
    public const STATUS_SELL = 1;
    public const STATUS_NOT_ACTIVE = 2;
    public const STATUS_BOOKED = 3;
    public const STATUS_SOLD = 4;

    //public const STATUS_RELOCATION  = 5; // deprecated
    public const STATUS_IN_DELIVERY = 6;
    public const STATUS_DELIVERED = 7;

    public const STATUSES = [
        self::STATUS_SELL        => 'Продается',
        self::STATUS_NOT_ACTIVE  => 'Не активен',
        self::STATUS_BOOKED      => 'Забронирован',
        self::STATUS_SOLD        => 'Продан',
        self::STATUS_IN_DELIVERY => 'Доставляется',
        self::STATUS_DELIVERED   => 'Доставлен',
    ];

    public const EDITABLE_STATUSES = [
        self::STATUS_SELL,
        self::STATUS_NOT_ACTIVE,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::STATUSES);

        Assert::true(in_array($value, $values, true), 'Wrong status');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::STATUSES[$this->value];
    }

    public function jsonSerialize(): int
    {
        return $this->getValue();
    }

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this->value, self::EDITABLE_STATUSES, true);
    }
}

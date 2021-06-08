<?php

declare(strict_types=1);

namespace App\Services\ProductType\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class DeliveryType.
 */
final class DeliveryType implements JsonSerializable
{
    public const TREASURE = 1;
    public const TAXI = 2;
    public const HOT_TREASURE = 3;
    public const VALUES = [
        self::TREASURE     => 'Клад',
        self::TAXI         => 'Такси',
        self::HOT_TREASURE => 'Горячий клад',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::VALUES);

        Assert::true(in_array($value, $values, true), 'Wrong delivery type');

        $this->value = $value;
    }

    public static function hotTypes(): array
    {
        return [
            self::TAXI,
            self::HOT_TREASURE,
        ];
    }

    public static function treasure(): self
    {
        return new self(self::TREASURE);
    }

    public static function taxi(): self
    {
        return new self(self::TAXI);
    }

    public static function hotTreasure(): self
    {
        return new self(self::HOT_TREASURE);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::VALUES[$this->value];
    }

    public function isTreasure(): bool
    {
        return $this->value === self::TREASURE;
    }

    public function isTaxi(): bool
    {
        return $this->value === self::TAXI;
    }

    public function isHotTreasure(): bool
    {
        return $this->value === self::HOT_TREASURE;
    }

    public function isNotTreasure(): bool
    {
        return $this->isTaxi() || $this->isHotTreasure();
    }

    public function jsonSerialize(): int
    {
        return $this->getValue();
    }
}

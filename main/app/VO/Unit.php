<?php

declare(strict_types=1);

namespace App\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class Unit.
 */
final class Unit implements JsonSerializable
{
    public const PIECES = 1;
    public const GRAM = 2;
    public const UNITS = [
        self::PIECES => 'шт',
        self::GRAM   => 'г',
    ];

    public const CASES = [
        self::PIECES => 'в штуках',
        self::GRAM   => 'в граммах',
    ];

    public const ROUND_PRECISIONS = [
        self::PIECES => 0,
        self::GRAM   => 2,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::UNITS);

        Assert::true(in_array($value, $values, true), 'Wrong unit');

        $this->value = $value;
    }

    public function getReadableValue(): string
    {
        return self::UNITS[$this->value];
    }

    public function getCaseValue(): string
    {
        return self::CASES[$this->value];
    }

    public function getRoundPrecision(): int
    {
        return self::ROUND_PRECISIONS[$this->value];
    }

    /**
     * @return int
     */
    public function jsonSerialize(): int
    {
        return $this->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

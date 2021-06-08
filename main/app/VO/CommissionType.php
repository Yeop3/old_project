<?php

declare(strict_types=1);

namespace App\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class CommissionType.
 */
final class CommissionType implements JsonSerializable
{
    public const TYPE_FIXED = 1;
    public const TYPE_PERCENT = 2;
    public const TYPES = [
        self::TYPE_FIXED   => 'грн',
        self::TYPE_PERCENT => '%',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong commission type');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::TYPES[$this->value];
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

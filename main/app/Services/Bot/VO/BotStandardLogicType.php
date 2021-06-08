<?php

declare(strict_types=1);

namespace App\Services\Bot\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class BotStandardLogicType.
 */
final class BotStandardLogicType implements JsonSerializable
{
    public const TELEGRAM = 1;
    public const TYPES = [
        self::TELEGRAM,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong standard logic type');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

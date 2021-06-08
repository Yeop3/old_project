<?php

declare(strict_types=1);

namespace App\Services\Bot\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class BotLogicType.
 */
final class BotLogicType implements JsonSerializable
{
    public const STANDARD = 1;
    public const CLIENT = 2;
    public const TYPES = [
        self::STANDARD,
        self::CLIENT,
    ];

    private int $value;

    public function __construct(int $value)
    {
        Assert::true(in_array($value, self::TYPES, true), 'Wrong logic type');

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

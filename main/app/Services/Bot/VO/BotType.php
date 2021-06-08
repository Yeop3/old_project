<?php

declare(strict_types=1);

namespace App\Services\Bot\VO;

use Webmozart\Assert\Assert;

/**
 * Class BotType.
 */
final class BotType
{
    public const STANDARD = 1;
    public const CLIENT = 2;
    public const TYPES = [
        self::STANDARD => 'Стандартный',
        self::CLIENT   => 'Пользовательский',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong bot type');

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
}

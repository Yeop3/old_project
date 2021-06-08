<?php

declare(strict_types=1);

namespace App\Services\Bot\VO;

use Webmozart\Assert\Assert;

/**
 * Class Messenger.
 */
final class Messenger
{
    public const TELEGRAM = 1;
    public const MESSENGERS = [
        self::TELEGRAM => 'Telegram',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::MESSENGERS);

        Assert::true(in_array($value, $values, true), 'Wrong messenger');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::MESSENGERS[$this->value];
    }
}

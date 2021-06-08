<?php

declare(strict_types=1);

namespace App\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class SourceType.
 */
final class SourceType implements JsonSerializable
{
    public const TYPE_BOT = 'bot';
    public const TYPE_SITE = 'site';
    public const TYPES = [
        self::TYPE_BOT  => 'Бот',
        self::TYPE_SITE => 'Сайт',
    ];

    private string $value;

    public function __construct(string $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong source type');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::TYPES[$this->value];
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}

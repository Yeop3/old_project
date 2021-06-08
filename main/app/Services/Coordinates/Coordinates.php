<?php

declare(strict_types=1);

namespace App\Services\Coordinates;

use JsonSerializable;
use Stringable;
use Webmozart\Assert\Assert;

/**
 * Class Coordinates.
 */
final class Coordinates implements Stringable, JsonSerializable
{
    public const REGEX = "/[-]?((([0-8]?[0-9])(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d{1,8}))?)|180(\.0+)?)/";
    public const STRICT_REGEX = "/^[-]?((([0-8]?[0-9])(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d{1,8}))?)|180(\.0+)?)$/";

    private string $value;

    public function __construct(string $value)
    {
        Assert::regex(
            $value,
            self::STRICT_REGEX,
            'Wrong coordinates '.$value
        );

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLatLng(): array
    {
        return explode(',', $this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}

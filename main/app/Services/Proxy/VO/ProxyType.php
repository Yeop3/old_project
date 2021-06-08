<?php

declare(strict_types=1);

namespace App\Services\Proxy\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class ProxyType.
 */
final class ProxyType implements JsonSerializable
{
    public const SOCKS5 = 'socks5';
    public const HTTP = 'http';
    public const HTTPS = 'https';

    public const VALUES = [
        self::SOCKS5,
        self::HTTP,
        self::HTTPS,
    ];

    private string $value;

    public function __construct(string $value)
    {
        Assert::true(in_array($value, self::VALUES, true), 'Wrong value');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isSocks5(): bool
    {
        return self::SOCKS5 === $this->value;
    }

    public function isHttp(): bool
    {
        return self::HTTP === $this->value;
    }

    public function isHttps(): bool
    {
        return self::HTTPS === $this->value;
    }

    public static function makeSocks5(): self
    {
        return new self(self::SOCKS5);
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}

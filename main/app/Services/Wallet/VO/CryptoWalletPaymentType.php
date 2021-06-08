<?php

declare(strict_types=1);

namespace App\Services\Wallet\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class CryptoWalletPaymentType.
 */
final class CryptoWalletPaymentType implements JsonSerializable
{
    public const ROTATE = 1;
    public const BITAPS = 2;
    public const VALUES = [
        self::ROTATE => 'Один зазаз - один кошелек',
        self::BITAPS => 'Bitaps',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::VALUES);

        Assert::true(in_array($value, $values, true), 'Wrong resolving type');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::VALUES[$this->value];
    }

    /**
     * @return int|mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}

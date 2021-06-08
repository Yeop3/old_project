<?php

declare(strict_types=1);

namespace App\VO;

use JsonSerializable;
use Money\Currency;
use Webmozart\Assert\Assert;

/**
 * Class CryptoCurrency.
 */
final class CryptoCurrency implements JsonSerializable
{
    public const BTC = 'btc';
    public const BCH = 'bch';
    public const LTC = 'ltc';
    public const ETH = 'eth';

    public const VALUES = [
        self::BTC,
        self::BCH,
        self::LTC,
        self::ETH,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower($value);

        Assert::true(in_array($value, self::VALUES, true), 'Wrong currency');

        $this->value = $value;
    }

    public static function has(string $code): bool
    {
        foreach (self::VALUES as $value) {
            if (mb_strtolower($code) === mb_strtolower($value)) {
                return true;
            }
        }

        return false;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getRubPair(): string
    {
        return $this->getUpperCaseValue().'_UAH';
    }

    public function getUpperCaseValue(): string
    {
        return strtoupper($this->value);
    }

    public function toMoneyCurrency(): Currency
    {
        return new Currency($this->getUpperCaseValue());
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}

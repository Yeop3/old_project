<?php

declare(strict_types=1);

namespace App\Services\Currency;

use App\VO\CryptoCurrency;
use ArrayIterator;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * Class CryptoCurrencies.
 */
final class CryptoCurrencies implements Currencies
{
    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency): bool
    {
        return CryptoCurrency::has($currency->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function subunitFor(Currency $currency): int
    {
        if (!$this->contains($currency)) {
            throw new UnknownCurrencyException(
                $currency->getCode().' is not crypto and is not supported by this currency repository'
            );
        }

        return 8;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator(
            collect(CryptoCurrency::VALUES)
                ->map(fn (string $code) => new Currency($code))
                ->toArray()
        );
    }
}

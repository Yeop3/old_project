<?php

declare(strict_types=1);

namespace App\Services\Wallet\VO;

use Webmozart\Assert\Assert;

/**
 * Class CryptoWalletResolvingType.
 */
final class CryptoWalletResolvingType
{
    public const ONLY_ROTATE = 'only_rotate';
    public const ONLY_BITAPS = 'only_bitaps';
    public const ROTATE_AND_BITAPS = 'rotate_and_bitaps';

    public const VALUES = [
        self::ONLY_ROTATE       => 'Только "Один заказ - один кошелек" кошельки',
        self::ONLY_BITAPS       => 'Только Bitaps кошельки',
        self::ROTATE_AND_BITAPS => '"Один зазаз - один кошелек" кошельки, Bitaps кошельки когда закончатся первые',
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
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\VO;

use Webmozart\Assert\Assert;

/**
 * Class QiwiTransactionIdentification.
 */
final class QiwiTransactionIdentification
{
    public const BY_NOTE = 1;
    public const ONE_ORDER_PER_WALLET = 2;

    public const VALUES = [
        self::BY_NOTE              => 'По примечанию в платеже',
        self::ONE_ORDER_PER_WALLET => 'Один заказ на кошелек',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::VALUES);

        Assert::true(in_array($value, $values, true), 'Wrong value');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}

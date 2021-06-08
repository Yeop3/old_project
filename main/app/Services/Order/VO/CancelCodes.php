<?php

declare(strict_types=1);

namespace App\Services\Order\VO;

use Webmozart\Assert\Assert;

/**
 * Class CancelCodes.
 */
final class CancelCodes
{
    public const DRIVER_NOT_FOUNT = 1;

    public const CODES = [
        self::DRIVER_NOT_FOUNT => 'Водитель не найден',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::CODES);

        Assert::true(in_array($value, $values, true), 'Wrong initiator');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return "На данный момент мы не можем обработать заказ. (Код ошибки: {$this->value})";
    }

    public function getText(): string
    {
        return self::CODES[$this->value];
    }
}

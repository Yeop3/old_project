<?php

declare(strict_types=1);

namespace App\Services\Order\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class OrderStatus.
 */
final class OrderStatus implements JsonSerializable
{
    public const STATUS_AWAITING_PAYMENT = 1;
    public const STATUS_PARTIALLY_PAID = 2;
    public const STATUS_PAID = 3;
    public const STATUS_CANCELED_BY_CLIENT = 4;
    public const STATUS_CANCELED_BY_OPERATOR = 5;
    public const STATUS_CANCELED_BY_TIMEOUT = 6;
    public const STATUS_CANCELED_BY_SYSTEM = 7;
    public const STATUS_GIVEN = 8;
    /**
     * @deprecated
     */
    public const STATUS_RELOCATION = 9;

    public const STATUS_IN_DELIVERY = 10;
    public const STATUS_DELIVERED = 11;

    public const STATUSES = [
        self::STATUS_AWAITING_PAYMENT     => 'Ожидает оплату',
        self::STATUS_PARTIALLY_PAID       => 'Частично оплачен',
        self::STATUS_PAID                 => 'Оплачен',
        self::STATUS_CANCELED_BY_CLIENT   => 'Отменен клиентом',
        self::STATUS_CANCELED_BY_OPERATOR => 'Отменен оператором',
        self::STATUS_CANCELED_BY_TIMEOUT  => 'Отменен по таймауту',
        self::STATUS_CANCELED_BY_SYSTEM   => 'Отменен системой',
        self::STATUS_GIVEN                => 'Отдан оператором',
        self::STATUS_IN_DELIVERY          => 'Доставляется',
        self::STATUS_DELIVERED            => 'Доставлен',
    ];

    public const CANCELED_STATUS = [
        self::STATUS_CANCELED_BY_CLIENT,
        self::STATUS_CANCELED_BY_OPERATOR,
        self::STATUS_CANCELED_BY_TIMEOUT,
        self::STATUS_CANCELED_BY_SYSTEM,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::STATUSES);

        Assert::true(in_array($value, $values, true), 'Wrong status');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isCancelOrderStatus(): bool
    {
        $values = collect(self::CANCELED_STATUS);

        return $values->search($this->value, true) !== false;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Order\VO;

use App\Events\Order\OrderCanceledByClient;
use App\Events\Order\OrderCanceledByOperator;
use App\Events\Order\OrderCanceledByTimeout;
use Webmozart\Assert\Assert;

/**
 * Class CancelInitiator.
 */
final class CancelInitiator
{
    public const INITIATOR_CLIENT = 1;
    public const INITIATOR_TIMEOUT = 2;
    public const INITIATOR_SYSTEM = 3;
    public const INITIATOR_OPERATOR = 4;

    public const INITIATORS = [
        self::INITIATOR_CLIENT   => OrderStatus::STATUS_CANCELED_BY_CLIENT,
        self::INITIATOR_TIMEOUT  => OrderStatus::STATUS_CANCELED_BY_TIMEOUT,
        self::INITIATOR_SYSTEM   => OrderStatus::STATUS_CANCELED_BY_SYSTEM,
        self::INITIATOR_OPERATOR => OrderStatus::STATUS_CANCELED_BY_OPERATOR,
    ];

    public const EVENTS = [
        self::INITIATOR_TIMEOUT  => OrderCanceledByTimeout::class,
        self::INITIATOR_CLIENT   => OrderCanceledByClient::class,
        self::INITIATOR_OPERATOR => OrderCanceledByOperator::class,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::INITIATORS);

        Assert::true(in_array($value, $values, true), 'Wrong initiator');

        $this->value = $value;
    }

    public static function system(): self
    {
        return new self(self::INITIATOR_SYSTEM);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getStatus(): OrderStatus
    {
        return new OrderStatus(self::INITIATORS[$this->value]);
    }

    public function getEventClass(): ?string
    {
        return self::EVENTS[$this->value] ?? null;
    }
}

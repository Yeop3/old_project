<?php

namespace App\Services\Driver\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class PermissionTypes.
 */
class PermissionTypes implements JsonSerializable
{
    public const CREATE_PRODUCT_TYPE = 1;
    public const PROCESS_TAXI_ORDER = 2;
    public const PROCESS_HOT_ORDER = 3;
    public const TYPES = [
        self::CREATE_PRODUCT_TYPE => 'Создать товар',
        self::PROCESS_TAXI_ORDER  => 'Обработать доставку',
        self::PROCESS_HOT_ORDER   => 'Обработать горячий заказ',
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong permission type');

        $this->value = $value;
    }

    /**
     * @return array
     */
    public static function getInitPermissionForDrivers(): array
    {
        return array_keys(self::TYPES);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

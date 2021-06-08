<?php

declare(strict_types=1);

namespace App\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class Commission.
 */
final class Commission implements JsonSerializable
{
    private int            $value;
    private CommissionType $type;

    public function __construct(int $value, CommissionType $type)
    {
        if ($type->getValue() === CommissionType::TYPE_PERCENT) {
            Assert::range($value, 0, 100, 'Percent commission should be between 0 and 100');
        } else {
            Assert::true($value >= 0, 'Commission min value is 0');
        }

        $this->value = $value;
        $this->type = $type;
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->getValue(),
            'type'  => $this->getType(),
        ];
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getType(): CommissionType
    {
        return $this->type;
    }
}

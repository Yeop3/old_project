<?php

declare(strict_types=1);

namespace App\Services\Client\Update;

use App\Services\Discount\VO\DiscountValue;

/**
 * Class UpdateClientDto.
 */
final class UpdateClientDto
{
    private ?string $note;
    private DiscountValue $discountValue;
    private int $discountPriority;

    public function __construct(DiscountValue $discountValue, int $discountPriority, ?string $note = null)
    {
        $this->note = $note;
        $this->discountValue = $discountValue;
        $this->discountPriority = $discountPriority;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getDiscountValue(): DiscountValue
    {
        return $this->discountValue;
    }

    public function getDiscountPriority(): int
    {
        return $this->discountPriority;
    }
}

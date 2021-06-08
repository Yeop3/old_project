<?php

declare(strict_types=1);

namespace App\Services\Discount\VO;

use Carbon\Carbon;
use JsonSerializable;

/**
 * Class DiscountDateRange.
 */
final class DiscountDateRange implements JsonSerializable
{
    private ?Carbon $start;
    private ?Carbon $end;

    public function __construct(?Carbon $start = null, ?Carbon $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): ?Carbon
    {
        return $this->start;
    }

    public function getEnd(): ?Carbon
    {
        return $this->end;
    }

    /**
     * @return Carbon[]|null[]
     */
    public function jsonSerialize(): array
    {
        return [
            'start' => $this->getStart(),
            'end'   => $this->getEnd(),
        ];
    }
}

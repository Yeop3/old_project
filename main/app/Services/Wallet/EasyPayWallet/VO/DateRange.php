<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\VO;

use Carbon\Carbon;
use JsonSerializable;

/**
 * Class DateRange.
 */
final class DateRange implements JsonSerializable
{
    private Carbon $start;
    private Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    /**
     * @return Carbon[]
     */
    public function jsonSerialize(): array
    {
        return [
            'start' => $this->getStart(),
            'end'   => $this->getEnd(),
        ];
    }
}

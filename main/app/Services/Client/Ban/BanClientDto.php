<?php

declare(strict_types=1);

namespace App\Services\Client\Ban;

/**
 * Class BanClientDto.
 */
final class BanClientDto
{
    private int $days;

    /**
     * BanClientDto constructor.
     *
     * @param int $days
     */
    public function __construct(int $days)
    {
        $this->days = $days;
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }
}

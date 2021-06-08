<?php

declare(strict_types=1);

namespace App\Services\Client\MultiBan;

/**
 * Class MultiBanClientDto.
 */
final class MultiBanClientDto
{
    /**
     * @var array
     */
    private array $numbers;

    /**
     * @var int
     */
    private int $period;

    /**
     * MultiBanClientDto constructor.
     *
     * @param array $numbers
     * @param int   $period
     */
    public function __construct(array $numbers, int $period)
    {
        $this->numbers = $numbers;
        $this->period = $period;
    }

    /**
     * @return array
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }
}

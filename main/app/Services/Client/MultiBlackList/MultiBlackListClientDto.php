<?php

declare(strict_types=1);

namespace App\Services\Client\MultiBlackList;

/**
 * Class MultiBlackListClientDto.
 */
final class MultiBlackListClientDto
{
    /**
     * @var array
     */
    private array $numbers;

    /**
     * MultiBlackListClientDto constructor.
     *
     * @param array $numbers
     */
    public function __construct(array $numbers)
    {
        $this->numbers = $numbers;
    }

    /**
     * @return array
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }
}

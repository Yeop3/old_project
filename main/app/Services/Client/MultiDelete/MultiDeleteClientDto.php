<?php

declare(strict_types=1);

namespace App\Services\Client\MultiDelete;

/**
 * Class MultiDeleteClientDto.
 */
final class MultiDeleteClientDto
{
    /**
     * @var array
     */
    private array $numbers;

    /**
     * MultiDeleteClientDto constructor.
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

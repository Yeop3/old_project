<?php

declare(strict_types=1);

namespace App\Services\Product\ActionsSelectProduct;

/**
 * Class ActionsSelectProductDto.
 */
final class ActionsSelectProductDto
{
    private array $numbers;
    private string $type;

    /**
     * ActionsSelectProductDto constructor.
     *
     * @param array  $numbers
     * @param string $type
     */
    public function __construct(array $numbers, string $type)
    {
        $this->numbers = $numbers;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

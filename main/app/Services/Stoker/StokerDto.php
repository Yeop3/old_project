<?php

declare(strict_types=1);

namespace App\Services\Stoker;

/**
 * Class StokerDto.
 */
class StokerDto
{
    private int $clientNumber;
    private int $locationNumber;
    private int $sourceNumber;
    private int $product_typeNumber;

    public function __construct(int $sourceNumber, int $locationNumber, int $clientNumber, int $product_typeNumber)
    {
        $this->clientNumber = $clientNumber;
        $this->locationNumber = $locationNumber;
        $this->sourceNumber = $sourceNumber;
        $this->product_typeNumber = $product_typeNumber;
    }

    /**
     * @return int
     */
    public function getClientNumber(): int
    {
        return $this->clientNumber;
    }

    /**
     * @return int
     */
    public function getLocationNumber(): int
    {
        return $this->locationNumber;
    }

    /**
     * @return int
     */
    public function getProductTypeNumber(): int
    {
        return $this->product_typeNumber;
    }

    /**
     * @return int
     */
    public function getSourceNumber(): int
    {
        return $this->sourceNumber;
    }
}

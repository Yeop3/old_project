<?php

declare(strict_types=1);

namespace App\Services\Product\Create;

use App\Models\Product;
use JsonSerializable;

/**
 * Class MassCreateResult.
 */
final class MassCreateResult implements JsonSerializable
{
    private int $totalCount;
    private int $failedCount;
    /**
     * @var Product[]
     */
    private array $products;

    /**
     * MassCreateResult constructor.
     *
     * @param int       $totalCount
     * @param int       $failedCount
     * @param Product[] $products
     */
    public function __construct(int $totalCount, int $failedCount, array $products)
    {
        $this->totalCount = $totalCount;
        $this->failedCount = $failedCount;
        $this->products = $products;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'total_count'  => $this->totalCount,
            'failed_count' => $this->failedCount,
            'products'     => $this->products,
        ];
    }
}

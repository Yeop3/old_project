<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\VO;

/**
 * Class PageData.
 */
final class PageData
{
    private int $pageNumber;
    private int $countPerPage;

    public function __construct(int $pageNumber, int $countPerPage = 10)
    {
        $this->pageNumber = $pageNumber;
        $this->countPerPage = $countPerPage;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getCountPerPage(): int
    {
        return $this->countPerPage;
    }
}

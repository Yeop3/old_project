<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual\Update;

/**
 * Class UpdateQiwiManualWalletDto.
 */
final class UpdateQiwiManualWalletDto
{
    private bool $active;
    private int $minPaidOrdersCountForShowing;
    private ?string $note;

    public function __construct(bool $active, int $minPaidOrdersCountForShowing, ?string $note = null)
    {
        $this->active = $active;
        $this->minPaidOrdersCountForShowing = $minPaidOrdersCountForShowing;
        $this->note = $note;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getMinPaidOrdersCountForShowing(): int
    {
        return $this->minPaidOrdersCountForShowing;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}

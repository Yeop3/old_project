<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManual\Create;

use App\VO\Phone;

/**
 * Class CreateQiwiManualWalletDto.
 */
final class CreateQiwiManualWalletDto
{
    private Phone $phone;
    private bool $active;
    private int $minPaidOrdersCountForShowing;
    private ?string $note;

    public function __construct(Phone $phone, bool $active, int $minPaidOrdersCountForShowing, ?string $note = null)
    {
        $this->phone = $phone;
        $this->active = $active;
        $this->minPaidOrdersCountForShowing = $minPaidOrdersCountForShowing;
        $this->note = $note;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
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

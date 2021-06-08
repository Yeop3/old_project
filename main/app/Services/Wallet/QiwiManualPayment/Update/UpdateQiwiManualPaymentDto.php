<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManualPayment\Update;

use App\VO\Phone;
use Money\Money;

/**
 * Class UpdateQiwiManualPaymentDto.
 */
final class UpdateQiwiManualPaymentDto
{
    private Money $amount;
    private ?Phone $clientWallet;
    private ?string $comment;

    public function __construct(Money $amount, ?Phone $clientWallet = null, ?string $comment = null)
    {
        $this->amount = $amount;
        $this->clientWallet = $clientWallet;
        $this->comment = $comment;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getClientWallet(): ?Phone
    {
        return $this->clientWallet;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}

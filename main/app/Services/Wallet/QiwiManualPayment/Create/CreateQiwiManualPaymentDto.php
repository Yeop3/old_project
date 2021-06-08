<?php

declare(strict_types=1);

namespace App\Services\Wallet\QiwiManualPayment\Create;

use App\VO\Phone;
use Money\Money;

/**
 * Class CreateQiwiManualPaymentDto.
 */
final class CreateQiwiManualPaymentDto
{
    private int $orderNumber;
    private Money $amount;
    private ?Phone $clientWallet;
    private ?string $comment;

    public function __construct(int $orderNumber, Money $amount, ?Phone $clientWallet = null, ?string $comment = null)
    {
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->clientWallet = $clientWallet;
        $this->comment = $comment;
    }

    public function getOrderNumber(): int
    {
        return $this->orderNumber;
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

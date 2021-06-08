<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitaps;

/**
 * Class CreateBitapsPaymentAddressDto.
 */
final class CreateBitapsPaymentAddressDto
{
    private string $forwardingAddress;
    private ?string $callbackLink;
    private ?int $confirmations;

    /**
     * CreateBitapsPaymentAddressDto constructor.
     *
     * @param string      $forwardingAddress
     * @param string|null $callbackLink
     * @param int|null    $confirmations
     */
    public function __construct(string $forwardingAddress, ?string $callbackLink, ?int $confirmations)
    {
        $this->confirmations = $confirmations ?? 3;
        $this->forwardingAddress = $forwardingAddress;
        $this->callbackLink = $callbackLink;
        $this->confirmations = $confirmations;
    }

    public function getForwardingAddress(): string
    {
        return $this->forwardingAddress;
    }

    public function getCallbackLink(): ?string
    {
        return $this->callbackLink;
    }

    public function getConfirmations(): ?int
    {
        return $this->confirmations;
    }
}

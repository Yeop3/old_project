<?php

namespace App\Services\Wallet\Crypto\Update;

/**
 * Class CryptoWalletUpdateDto.
 */
class CryptoWalletUpdateDto
{
    private ?string $note;
    private ?int $proxyNumber;
    private string $name;
    private int $confirmations;

    public function __construct(?string $note, string $name, ?int $proxyNumber = null, int $confirmations)
    {
        $this->note = $note;
        $this->proxyNumber = $proxyNumber;
        $this->name = $name;
        $this->confirmations = $confirmations;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getProxyNumber(): ?int
    {
        return $this->proxyNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfirmations(): int
    {
        return $this->confirmations;
    }
}

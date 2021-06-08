<?php

declare(strict_types=1);

namespace App\Services\Wallet\Crypto\Create;

use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\VO\CryptoCurrency;

/**
 * Class CryptoWalletCreateDto.
 */
final class CryptoWalletCreateDto
{
    private string $address;
    private ?string $note;
    private ?int $proxyNumber;
    private string $name;
    private CryptoCurrency $currency;
    private CryptoWalletPaymentType $paymentType;
    private int $confirmations;

    public function __construct(string $address, ?string $note, string $name, CryptoCurrency $currency, CryptoWalletPaymentType $paymentType, ?int $proxyNumber = null, int $confirmations)
    {
        $this->address = $address;
        $this->note = $note;
        $this->proxyNumber = $proxyNumber;
        $this->name = $name;
        $this->currency = $currency;
        $this->paymentType = $paymentType;
        $this->confirmations = $confirmations;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
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

    public function getCurrency(): CryptoCurrency
    {
        return $this->currency;
    }

    public function getPaymentType(): CryptoWalletPaymentType
    {
        return $this->paymentType;
    }

    public function getConfirmations(): int
    {
        return $this->confirmations;
    }
}

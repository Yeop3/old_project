<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitaps;

/**
 * Class CreateForwardingAddressResponse.
 */
final class CreateForwardingAddressResponse
{
    public string $paymentCode;
    public ?string $callbackLink;
    public string $forwardingAddress;
    public ?string $domainHash;
    public ?int $confirmations;
    public string $address;
    public ?string $legacyAddress;
    public ?string $domain;
    public string $invoice;
    public string $currency;

    public function __construct(
        string $paymentCode,
        ?string $callbackLink,
        string $forwardingAddress,
        ?string $domainHash,
        ?int $confirmations,
        string $address,
        ?string $legacyAddress,
        ?string $domain,
        string $invoice,
        string $currency
    ) {
        $this->paymentCode = $paymentCode;
        $this->callbackLink = $callbackLink;
        $this->forwardingAddress = $forwardingAddress;
        $this->domainHash = $domainHash;
        $this->confirmations = $confirmations;
        $this->address = $address;
        $this->legacyAddress = $legacyAddress;
        $this->domain = $domain;
        $this->invoice = $invoice;
        $this->currency = $currency;
    }
}

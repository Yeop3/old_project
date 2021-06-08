<?php

declare(strict_types=1);

namespace App\Services\Wallet\Kuna\Update;

/**
 * Class KunaWalletUpdateDto.
 */
final class KunaWalletUpdateDto
{
    private string $name;
    private ?string $comment;
    private bool $active;
    private string $publicKey;
    private string $privateKey;
    private ?int $proxyNumber;

    public function __construct(string $name, ?string $comment, bool $active, string $publicKey, string $privateKey, ?int $proxyNumber = null)
    {
        $this->name = $name;
        $this->comment = $comment;
        $this->active = $active;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->proxyNumber = $proxyNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getProxyNumber(): ?int
    {
        return $this->proxyNumber;
    }
}

<?php

namespace App\Services\Wallet\GlobalMoney\Edit;

/**
 * Class GlobalMoneyWalletEditDto.
 */
final class GlobalMoneyWalletEditDto
{
    private string $login;

    private string $name;

    private ?string $password;

    private ?int $proxyNumber;

    private string $type;
    private string $walletNumber;
    private bool $active;

    public function __construct(string $walletNumber, string $login, string $name, ?string $password, string $type, bool $active, ?int $proxyNumber = null)
    {
        $this->login = $login;
        $this->name = $name;
        $this->password = $password;
        $this->proxyNumber = $proxyNumber;
        $this->type = $type;
        $this->walletNumber = $walletNumber;
        $this->active = $active;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getProxyNumber(): ?int
    {
        return $this->proxyNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWalletNumber(): string
    {
        return $this->walletNumber;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}

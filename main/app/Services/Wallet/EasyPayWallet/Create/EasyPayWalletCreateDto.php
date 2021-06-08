<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\Create;

/**
 * Class EasyPayWalletCreateDto.
 */
final class EasyPayWalletCreateDto
{
    private string $phone;

    private string $name;

    private string $password;

    private ?int $proxyNumber;

    private string $walletNumber;

    private float $limit;

    /**
     * EasyPayWalletCreateDto constructor.
     *
     * @param string   $phone
     * @param string   $name
     * @param string   $password
     * @param int|null $proxyNumber
     * @param string   $walletNumber
     * @param float    $limit
     */
    public function __construct(
        string $phone,
        string $name,
        string $password,
        ?int $proxyNumber,
        string $walletNumber,
        float $limit
    ) {
        $this->phone = $phone;
        $this->name = $name;
        $this->password = $password;
        $this->proxyNumber = $proxyNumber;
        $this->walletNumber = $walletNumber;
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) ($this->limit * 100);
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int|null
     */
    public function getProxyNumber(): ?int
    {
        return $this->proxyNumber;
    }

    /**
     * @return string
     */
    public function getWalletNumber(): string
    {
        return $this->walletNumber;
    }
}

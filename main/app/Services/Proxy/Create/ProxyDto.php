<?php

declare(strict_types=1);

namespace App\Services\Proxy\Create;

use App\Services\Proxy\VO\ProxyType;
use App\Services\Proxy\VO\ProxyVO;
use App\VO\Ip;

/**
 * Class ProxyDto.
 */
final class ProxyDto
{
    private Ip $ip;
    private int $port;
    private ?string $username;
    private ?string $password;
    private ProxyType $proxyType;
    private ?string $note;

    public function __construct(Ip $ip, int $port, ProxyType $proxyType, ?string $username = null, ?string $password = null, ?string $note = null)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->proxyType = $proxyType;
        $this->note = $note;
    }

    public function getIp(): Ip
    {
        return $this->ip;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getProxyType(): ProxyType
    {
        return $this->proxyType;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function toVO(): ProxyVO
    {
        return new ProxyVO($this->ip, $this->port, $this->proxyType, $this->username, $this->password);
    }
}

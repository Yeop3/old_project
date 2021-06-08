<?php

declare(strict_types=1);

namespace App\Services\Proxy\Update;

use App\Services\Proxy\VO\ProxyType;
use App\VO\Ip;

/**
 * Class UpdateProxyDto.
 */
class UpdateProxyDto
{
    private Ip $ip;
    private int $port;
    private ProxyType $proxyType;
    private ?string $note;

    public function __construct(Ip $ip, int $port, ProxyType $proxyType, ?string $note = null)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->proxyType = $proxyType;
        $this->note = $note;
    }

    public function getIp(): Ip
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getProxyType(): ProxyType
    {
        return $this->proxyType;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}

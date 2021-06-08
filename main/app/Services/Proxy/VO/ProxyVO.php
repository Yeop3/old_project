<?php

declare(strict_types=1);

namespace App\Services\Proxy\VO;

use App\VO\Ip;
use RuntimeException;

/**
 * Class ProxyVO.
 */
final class ProxyVO
{
    private Ip $ip;
    private int $port;
    private ?string $username;
    private ?string $password;
    private ProxyType $type;

    public function __construct(Ip $ip, int $port, ProxyType $type, ?string $username, ?string $password)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->type = $type;
    }

    public function toString(): string
    {
        if ($this->type->isSocks5()) {
            if ($this->username && $this->password) {
                return "socks5h://$this->username:$this->password@$this->ip:$this->port";
            }

            return "socks5h://$this->ip:$this->port";
        }

        if ($this->type->isHttp() || $this->type->isHttps()) {
            if ($this->username && $this->password) {
                return "$this->username:$this->password@$this->ip:$this->port";
            }

            return "$this->ip:$this->port";
        }

        throw new RuntimeException('unsupported proxy type '.$this->type->getValue());
    }
}

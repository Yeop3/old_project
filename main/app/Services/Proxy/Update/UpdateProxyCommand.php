<?php

declare(strict_types=1);

namespace App\Services\Proxy\Update;

use App\Models\Proxy;
use App\Services\Proxy\CheckProxyCommand;
use App\Services\Proxy\Create\ProxyDto;
use App\Services\Proxy\ProxyConnectionException;

/**
 * Class UpdateProxyCommand.
 */
class UpdateProxyCommand
{
    private CheckProxyCommand $checkProxyCommand;

    /**
     * UpdateProxyCommand constructor.
     *
     * @param CheckProxyCommand $checkProxyCommand
     */
    public function __construct(CheckProxyCommand $checkProxyCommand)
    {
        $this->checkProxyCommand = $checkProxyCommand;
    }

    /**
     * @param Proxy    $proxy
     * @param ProxyDto $dto
     *
     * @return Proxy
     */
    public function execute(Proxy $proxy, ProxyDto $dto): Proxy
    {
        $check = $this->checkProxyCommand->execute($dto->toVO());

        if (!$check) {
            throw new ProxyConnectionException('Прокси не прошел проверку');
        }

        $this->checkProxyCommand->execute($dto->toVO());

        $proxy->ip = $dto->getIp()->getValue();
        $proxy->port = $dto->getPort();
        $proxy->proxy_type = $dto->getProxyType();
        $proxy->username = $dto->getUsername();
        $proxy->password = $dto->getPassword();
        $proxy->note = $dto->getNote();

        $proxy->save();

        return $proxy;
    }
}

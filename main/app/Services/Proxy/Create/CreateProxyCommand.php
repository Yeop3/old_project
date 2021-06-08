<?php

declare(strict_types=1);

namespace App\Services\Proxy\Create;

use App\Models\Proxy;
use App\Models\Seller;
use App\Services\Proxy\CheckProxyCommand;
use App\Services\Proxy\ProxyConnectionException;

/**
 * Class CreateProxyCommand.
 */
class CreateProxyCommand
{
    private CheckProxyCommand $checkProxyCommand;

    public function __construct(CheckProxyCommand $checkProxyCommand)
    {
        $this->checkProxyCommand = $checkProxyCommand;
    }

    public function execute(Seller $seller, ProxyDto $dto): Proxy
    {
        $check = $this->checkProxyCommand->execute($dto->toVO());

        if (!$check) {
            throw new ProxyConnectionException('Прокси не прошел проверку');
        }

        $proxy = new Proxy();

        $proxy->ip = $dto->getIp()->getValue();
        $proxy->seller_id = $seller->id;
        $proxy->port = $dto->getPort();
        $proxy->username = $dto->getUsername();
        $proxy->password = $dto->getPassword();
        $proxy->proxy_type = $dto->getProxyType();
        $proxy->note = $dto->getNote();
        $proxy->is_working = 1;
        $proxy->save();

        return $proxy;
    }
}

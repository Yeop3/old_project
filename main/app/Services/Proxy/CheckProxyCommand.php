<?php

declare(strict_types=1);

namespace App\Services\Proxy;

use App\Services\Proxy\VO\ProxyVO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * Class CheckProxyCommand.
 */
class CheckProxyCommand
{
    public function execute(ProxyVO $proxy): bool
    {
        $client = new Client();

        try {
            $client->get(
                'https://www.google.com/',
                [
                    'proxy'       => $proxy->toString(),
                    'timeout'     => 10,
                    'http_errors' => false,
                ]
            );

            return true;
        } catch (ConnectException $e) {
            return false;
        }
    }
}

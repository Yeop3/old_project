<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet;

use App\Events\Wallet\EasyPayLoginFailEvent;
use App\Models\Proxy;
use App\Services\Wallet\EasyPayWallet\Exceptions\EasyPayHttpException;
use App\Services\Wallet\EasyPayWallet\VO\DateRange;
use App\Services\Wallet\EasyPayWallet\VO\EasyPayLoginData;
use App\Services\Wallet\EasyPayWallet\VO\PageData;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * Class EasyPayApi.
 */
final class EasyPayApi
{
    private Client $client;
    private ?EasyPayLoginData $loginData = null;
    private ?string $token = null;
    private ?Proxy $proxy = null;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
            'headers'     => [
                'partnerkey' => 'easypay-v2',
                'appid'      => Uuid::uuid4()->toString(),
                'pageid'     => 'ea6160d9-773c-44a9-9f5d-41c243e65452',
                'timeout'    => 5,
                //                'Accept' => 'application/json',
                //                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function setProxy(Proxy $proxy): self
    {
        $this->proxy = $proxy;

        return $this;
    }

    public function login(): self
    {
        if (!$this->loginData) {
            throw new RuntimeException('you should set login data');
        }

        $loginBody = http_build_query(array_merge($this->loginData->toArrayForApi(), [
            'grant_type' => 'password',
            'client_id'  => 'easypay-v2',
        ]));

        $response = $this->client->post('https://api.easypay.ua/api/token', [
            'body'    => $loginBody,
            'headers' => [
            ],
            'proxy' => $this->proxy->toString(),
        ]);

        if ($response->getStatusCode() !== 200) {
            event(new EasyPayLoginFailEvent($this->loginData));

            throw new EasyPayHttpException("easypay auth error - {$response->getBody()->getContents()}");
        }

        $this->token = json_decode($response->getBody()->getContents(), true)['access_token'];

        return $this;
    }

    public function getWallets(): array
    {
        if (!$this->loginData) {
            throw new EasyPayHttpException('you should set login data');
        }

        if (!$this->token) {
            throw new EasyPayHttpException('you should to login');
        }

        $response = $this->client->get('https://api.easypay.ua/api/wallets/get', [
            'headers' => [
                'appid'         => '200f0e73-5089-4649-87d1-0c52a35780b1',
                'authorization' => 'bearer '.$this->token,
            ],
            'proxy' => $this->proxy->toString(),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new EasyPayHttpException("easypay get wallets error - {$response->getStatusCode()} {$response->getBody()->getContents()}");
        }

        return json_decode($response->getBody()->getContents(), true)['wallets'];
    }

    public function getWalletTransactions(int $walletId, DateRange $dateRange, PageData $pageData): array
    {
        if (!$this->loginData) {
            throw new EasyPayHttpException('you should set login data');
        }

        if (!$this->token) {
            throw new EasyPayHttpException('you should to login');
        }

        $query = http_build_query([
            'dateStart'    => $dateRange->getStart()->toDateString(),
            'dateEnd'      => $dateRange->getEnd()->toDateString(),
            'countPerPage' => $pageData->getCountPerPage(),
            'pageNumber'   => $pageData->getPageNumber(),
            'amountFrom'   => 0,
            'amountTo'     => 1000000,
            'instrumentId' => $walletId,
        ]);

        $response = $this->client->get("https://api.easypay.ua/api/history/instruments/$walletId?$query", [
            'headers' => [
                'appid'         => '200f0e73-5089-4649-87d1-0c52a35780b1',
                'authorization' => 'bearer '.$this->token,
            ],
            'proxy' => $this->proxy->toString(),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new EasyPayHttpException("easypay get transactions error - {$response->getStatusCode()} {$response->getBody()->getContents()}");
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getWalletStatements(int $walletId, int $month, int $year): array
    {
        if (!$this->loginData) {
            throw new EasyPayHttpException('you should set login data');
        }

        if (!$this->token) {
            throw new EasyPayHttpException('you should to login');
        }

        $response = $this->client->get("https://api.easypay.ua/api/wallets/statement/$walletId/$month/$year", [
            'headers' => [
                'appid'         => '200f0e73-5089-4649-87d1-0c52a35780b1',
                'authorization' => 'bearer '.$this->token,
            ],
            'proxy' => $this->proxy->toString(),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new EasyPayHttpException("easypay get transactions error - {$response->getStatusCode()} {$response->getBody()->getContents()}");
        }

        return json_decode($response->getBody()->getContents(), true)['walletStatement']['statements'];
    }

    public function setLoginData(EasyPayLoginData $loginData): self
    {
        $this->loginData = $loginData;

        return $this;
    }

    public function isLoggedIn(): bool
    {
        return $this->token !== null && $this->loginData !== null;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney;

use App\Events\GlobalMoney\GlobalMoneyLoginFailed;
use App\Services\Wallet\GlobalMoney\Exceptions\GlobalMoneyHttpException;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class GlobalMoneyApi.
 */
final class GlobalMoneyApi
{
    private Client $client;
    private ?GlobalMoneyLoginData $loginData = null;
    private ?string $token = null;

    /**
     * GlobalMoneyApi constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
            'headers'     => [
                'x-gm-gaid'  => random_int(11111111, 999999999),
                'x-gm-print' => Str::random(32),

                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * @return $this
     */
    public function login(): self
    {
        if (!$this->loginData) {
            throw new RuntimeException('you should set login data');
        }

        $response = $this->client->post('https://art.global24.ua/login', [
            'json' => $this->loginData->toArrayForApi(),
        ]);

        $notErrorStatuses = collect([200, 403]);

        if ($response->getStatusCode() === 403) {
            event(new GlobalMoneyLoginFailed($this->loginData));
            $responseData = json_decode($response->getBody()->getContents(), true);
            $error = $responseData['errorText'][2]['value'];

            throw new GlobalMoneyHttpException((string) ($error));
        }

        if (!$notErrorStatuses->has($response->getStatusCode())) {
            throw new GlobalMoneyHttpException('You have some problem with access');
        }
        $tokenData = collect(json_decode($response->getBody()->getContents(), true));
        $this->token = $tokenData->get('token');

        return $this;
    }

    /**
     * @param Carbon|null $dateTill
     *
     * @return array
     */
    public function getTransactions(?Carbon $dateTill = null): array
    {
        if (!$this->loginData) {
            throw new GlobalMoneyHttpException('you should set login data');
        }

        if (!$this->token) {
            throw new GlobalMoneyHttpException('you should to login');
        }

        $data = [
            'dateFrom' => '0',
        ];

        if ($dateTill) {
            $data['dateTill'] = $dateTill->toDateTimeString();
        }

        $query = http_build_query($data);

        $response = $this->client->get('https://art.global24.ua/transactions?'.$query, [
            'headers' => [
                'authorization' => $this->token,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new GlobalMoneyHttpException("global money get transactions error - {$response->getStatusCode()} {$response->getBody()->getContents()}");
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param GlobalMoneyLoginData $loginData
     *
     * @return $this
     */
    public function setLoginData(GlobalMoneyLoginData $loginData): self
    {
        $this->loginData = $loginData;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->token !== null && $this->loginData !== null;
    }
}

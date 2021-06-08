<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Events\Payment\WrongPaymentCodeGot;
use App\Models\Bot;
use App\Models\Client;
use App\Services\Client\ClientResolver;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeActivationErrorException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeCurrencyIsNotUahException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeIsNotActiveException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeNotFoundException;
use App\Services\Wallet\Kuna\Exceptions\KunaCodeRecepientIsNotAllException;
use App\Services\Wallet\Kuna\HandleKunaCodeCommand;
use BotMan\BotMan\BotMan;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class KunaCodeHandler.
 */
final class KunaCodeHandler implements BotHandler
{
    /**
     * @var ClientResolver
     */
    private ClientResolver $clientResolver;
    /**
     * @var HandleKunaCodeCommand
     */
    private HandleKunaCodeCommand $handleKunaCodeCommand;
    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * KunaCodeHandler constructor.
     *
     * @param ClientResolver        $clientResolver
     * @param HandleKunaCodeCommand $handleKunaCodeCommand
     * @param Dispatcher            $dispatcher
     */
    public function __construct(
        ClientResolver $clientResolver,
        HandleKunaCodeCommand $handleKunaCodeCommand,
        Dispatcher $dispatcher
    ) {
        $this->clientResolver = $clientResolver;
        $this->handleKunaCodeCommand = $handleKunaCodeCommand;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param BotMan      $botMan
     * @param Bot         $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        if (!$client->order) {
            return;
        }

        $code = $params[0] ?? null;

        if (!$code) {
            $this->handleKunaCodeNotFound($botMan, $client);

            return;
        }

        $segments = mb_substr_count($code, '-');

        if ($segments < 9) {
            $this->handleKunaCodeNotFound($botMan, $client);

            return;
        }

        try {
            $this->handleKunaCodeCommand->execute($client, $code);
        } catch (KunaCodeNotFoundException $e) {
            $this->handleKunaCodeNotFound($botMan, $client);

            return;
        } catch (KunaCodeIsNotActiveException $e) {
            $this->handleKunaCodeIsNotActive($botMan, $client);

            return;
        } catch (KunaCodeCurrencyIsNotUahException $e) {
            $this->handleKunaCodeCurrencyIsNotUah($botMan, $client);

            return;
        } catch (KunaCodeRecepientIsNotAllException $e) {
            $this->handleKunaCodeRecepientIsNotAll($botMan, $client);

            return;
        } catch (KunaCodeActivationErrorException $e) {
            $this->handleKunaCodeActivationError($botMan, $client);

            return;
        }
    }

    /**
     * @param BotMan $botMan
     * @param Client $client
     */
    private function handleKunaCodeNotFound(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'Kuna-код не найден',
            ['parse_mode' => 'HTML']
        );

        $this->handleWrongCode($client);
    }

    /**
     * @param Client $client
     */
    private function handleWrongCode(Client $client): void
    {
        $this->dispatcher->dispatch(new WrongPaymentCodeGot($client));
    }

    /**
     * @param BotMan $botMan
     * @param Client $client
     */
    private function handleKunaCodeIsNotActive(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'Kuna-код не активен',
            ['parse_mode' => 'HTML']
        );

        $this->handleWrongCode($client);
    }

    /**
     * @param BotMan $botMan
     * @param Client $client
     */
    private function handleKunaCodeCurrencyIsNotUah(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'Принимаются Kuna-коды только в UAH валюте',
            ['parse_mode' => 'HTML']
        );

        $this->handleWrongCode($client);
    }

    /**
     * @param BotMan $botMan
     * @param Client $client
     */
    private function handleKunaCodeRecepientIsNotAll(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'У Kuna-кода не должно быть конкретного получателя',
            ['parse_mode' => 'HTML']
        );

        $this->handleWrongCode($client);
    }

    /**
     * @param BotMan $botMan
     * @param Client $client
     */
    private function handleKunaCodeActivationError(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'При активации Kuna-кода произошла ошибка',
            ['parse_mode' => 'HTML']
        );

        $this->handleWrongCode($client);
    }
}

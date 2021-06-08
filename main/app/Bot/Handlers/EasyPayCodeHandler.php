<?php

namespace App\Bot\Handlers;

use App\Events\Payment\WrongPaymentCodeGot;
use App\Models\Bot;
use App\Models\Client;
use App\Services\Client\ClientResolver;
use App\Services\Wallet\EasyPayWallet\Exceptions\WrongEasyPayCodeException;
use App\Services\Wallet\EasyPayWallet\HandleEasyPayCodeCommand;
use BotMan\BotMan\BotMan;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use JsonException;

/**
 * Class EasyPayCodeHandler.
 */
class EasyPayCodeHandler implements BotHandler
{
    private ClientResolver           $clientResolver;
    private HandleEasyPayCodeCommand $handleEasyPayCodeCommand;
    private Dispatcher               $dispatcher;

    /**
     * EasyPayCodeHandler constructor.
     *
     * @param ClientResolver           $clientResolver
     * @param HandleEasyPayCodeCommand $handleEasyPayCodeCommand
     * @param Dispatcher               $dispatcher
     */
    public function __construct(
        ClientResolver $clientResolver,
        HandleEasyPayCodeCommand $handleEasyPayCodeCommand,
        Dispatcher $dispatcher
    ) {
        $this->clientResolver = $clientResolver;
        $this->handleEasyPayCodeCommand = $handleEasyPayCodeCommand;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param BotMan $botMan
     * @param Bot $botModel
     * @param string|null ...$params
     * @throws JsonException
     * @throws \Throwable
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
            $this->handleCodeNotFound($botMan, $client);

            return;
        }

        try {
            $this->handleEasyPayCodeCommand->execute(
                $client,
                $code,
            );
        } catch (WrongEasyPayCodeException $e) {
            $this->handleCodeNotFound($botMan, $client);

            Log::channel('easy_pay_checks')
                ->info('wrong code', [
                    'client_id' => $client->id,
                    'order_id'  => $client->order->id ?? null,
                    'code'      => $code,
                ]);

            return;
        } catch (Exception $e) {
            $this->handleError($botMan);

            Log::channel('easy_pay_checks')->error(
                json_encode([
                    $e->getMessage(),
                    $e->getTrace(),
                ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
            );

            return;
        }
    }

    private function handleCodeNotFound(BotMan $botMan, Client $client): void
    {
        $botMan->reply(
            'Код не найден',
            ['parse_mode' => 'HTML']
        );

        $this->dispatcher->dispatch(new WrongPaymentCodeGot($client));
    }

    private function handleError(BotMan $botMan): void
    {
        $botMan->reply(
            'Произошла ошибка при проверке кода',
            ['parse_mode' => 'HTML']
        );
    }
}

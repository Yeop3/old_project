<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Events\Payment\WrongPaymentCodeGot;
use App\Models\Bot;
use App\Models\Client;
use App\Services\Client\ClientResolver;
use App\Services\Wallet\GlobalMoney\Exceptions\GlobalMoneyHttpException;
use App\Services\Wallet\GlobalMoney\Exceptions\WrongGlobalMoneyCodeException;
use App\Services\Wallet\GlobalMoney\HandleGlobalMoneyCodeCommand;
use BotMan\BotMan\BotMan;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

/**
 * Class GlobalMoneyCodeHandler.
 */
final class GlobalMoneyCodeHandler implements BotHandler
{
    private ClientResolver               $clientResolver;
    private HandleGlobalMoneyCodeCommand $handleGlobalMoneyCardCodeCommand;
    private Dispatcher                   $dispatcher;

    public function __construct(
        ClientResolver $clientResolver,
        HandleGlobalMoneyCodeCommand $handleGlobalMoneyCardCodeCommand,
        Dispatcher $dispatcher
    ) {
        $this->clientResolver = $clientResolver;
        $this->handleGlobalMoneyCardCodeCommand = $handleGlobalMoneyCardCodeCommand;
        $this->dispatcher = $dispatcher;
    }

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
            $this->handleGlobalMoneyCardCodeCommand->execute(
                $client,
                $code,
            );
        } catch (WrongGlobalMoneyCodeException $e) {
            $this->handleCodeNotFound($botMan, $client);

            return;
        } catch (GlobalMoneyHttpException $e) {
            $this->handleError($botMan);

            Log::channel('global_money_card_checks')->error(
                json_encode(
                    [
                        $e->getMessage(),
                        $e->getTrace(),
                    ],
                    JSON_PRETTY_PRINT
                )
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

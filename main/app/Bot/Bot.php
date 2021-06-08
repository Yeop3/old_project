<?php

namespace App\Bot;

use App\Bot\Handlers\ClientHandler;
use App\Bot\Handlers\EasyPayCodeHandler;
use App\Bot\Handlers\GlobalMoneyCodeHandler;
use App\Bot\Handlers\KunaCodeHandler;
use App\Bot\Handlers\OrderCancelHandler;
use App\Bot\Handlers\OrderCheckHandler;
use App\Bot\Handlers\OrderHandler;
use App\Bot\Middleware\AttachCommonButtonsMiddleware;
use App\Bot\Middleware\HandleClientMiddleware;
use App\Bot\Middleware\HandleTextMiddleware;
use App\Bot\Middleware\LogMiddleware;
use App\Events\SellerBotMessageReceived;
use App\Services\Client\ClientResolver;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Exceptions\Base\BotManException;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Bot.
 */
class Bot {

    private BotMan          $botman;
    private \App\Models\Bot $botModel;

    public function __construct(\App\Models\Bot $botModel, ?Request $request = null)
    {
        DriverManager::loadDriver(TelegramDriver::class);

        $storage = new FileStorage(storage_path('botman'));
        $config  = [
            'telegram'        => [
                'token' => $botModel->token,
            ],
            'user_cache_time' => now()->addMinutes(5),
        ];

        $this->botModel = $botModel;

        $this->botman = BotManFactory::create(
            $config,
            new LaravelCache(),
            $request,
            $storage
        );

        $this->registerMiddleware();
    }

    /**
     * @throws BindingResolutionException
     */
    private function registerMiddleware(): void
    {
        $this->botman
            ->middleware
            ->heard(
                new HandleClientMiddleware(
                    app()->make(ClientHandler::class),
                    $this->botModel
                )
            )
            ->sending(
                new HandleTextMiddleware(),
                new AttachCommonButtonsMiddleware(
                    $this->botModel,
                    app()->make(GetCommonButtons::class)
                ),
                new LogMiddleware(
                    $this->botModel,
                    app()->make(ClientResolver::class),
                    app()->make(Dispatcher::class),
                ),
            );
    }

    /**
     * @throws BindingResolutionException
     */
    public function webHook(): void
    {
        foreach (HandlersMapper::MAP as $command => $handlerClass) {
            $handler = app()->make($handlerClass);

            if ($command === 'ANYKEY') {
                $this->botman->fallback(function (BotMan $botMan, string ...$params) use ($handler) {
                    app()->make(
                        ClientHandler::class
                    )->execute(
                        $botMan,
                        $this->botModel,
                        ...$params
                    );

                    if ($this->clientIsBaned($botMan)) {
                        return;
                    }

                    $this->dispatchMessageReceived($botMan);

                    if ($this->getClientOrder($botMan)) {
                        return;
                    }

                    $isNeedToStopHearing = $this->handleClientHasOrderAndCheckIsNeedToStopHearing(
                        $botMan,
                        $botMan->getMessage()->getText() ?: '',
                        ...$params
                    );

                    if ($isNeedToStopHearing) {
                        return;
                    }

                    $handler->execute($botMan, $this->botModel);
                });

                continue;
            }

            $this->botman->hears($command, function (BotMan $botMan, string ...$params) use ($handler) {
                if ($this->clientIsBaned($botMan)) {
                    return;
                }

                $this->dispatchMessageReceived($botMan);

                $this->clearPreOrder($botMan);

                if (!($handler instanceof OrderCancelHandler) && !($handler instanceof OrderCheckHandler)) {
                    $isNeedToStopHearing = $this->handleClientHasOrderAndCheckIsNeedToStopHearing(
                        $botMan,
                        $botMan->getMessage()->getText(),
                        ...$params
                    );

                    if ($isNeedToStopHearing) {
                        return;
                    }
                }

                $handler->execute($botMan, $this->botModel, ...$params);
            });
        }

        $this->botman->listen();
    }

    /**
     * @param BotMan $botMan
     *
     * @return bool
     * @throws BindingResolutionException
     *
     */
    private function clientIsBaned(BotMan $botMan): bool
    {
        $client = app()->make(ClientResolver::class)
            ->resolve(
                $botMan->getUser(),
                $botMan->getDriver()->getName(),
                $this->botModel->seller_id
            );

        if (!$client) {
            return false;
        }

        if ($client->in_black_list) {
            return true;
        }

        if (!$client->ban_expires_at) {
            return false;
        }

        return now()->lt($client->ban_expires_at);
    }

    /**
     * @param BotMan $botMan
     *
     * @throws BindingResolutionException
     */
    private function dispatchMessageReceived(BotMan $botMan): void
    {
        $client = app()->make(ClientResolver::class)
            ->resolve(
                $botMan->getUser(),
                $botMan->getDriver()->getName(),
                $this->botModel->seller_id
            );

        if ($client) {
            SellerBotMessageReceived::dispatch($client, $botMan->getMessage());
        }
    }

    /**
     * @param BotMan $botMan
     *
     * @return bool
     * @throws BindingResolutionException
     *
     */
    private function getClientOrder(BotMan $botMan): bool
    {
        $client = app()->make(
            ClientResolver::class
        )->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $this->botModel->seller_id
        );

        if (!$client) {
            return false;
        }

        if (!$client->pre_order) {
            return false;
        }

        $payment = $botMan->getMessage()->getText();

        if (!in_array($payment, PaymentMethod::TYPES)) {
            return false;
        }

        $payment = array_search($payment, PaymentMethod::TYPES);

        app(OrderHandler::class)->execute(
            $botMan,
            $this->botModel,
            $client->pre_order['product_type'],
            $client->pre_order['location'],
            $payment
        );

        $client->pre_order = null;
        $client->save();

        return true;
    }

    /**
     * @param BotMan $botMan
     * @param string ...$params
     *
     * @return bool
     * @throws Throwable
     * @throws BindingResolutionException
     */
    private function handleClientHasOrderAndCheckIsNeedToStopHearing(BotMan $botMan, string ...$params): bool
    {
        $client = app()->make(ClientResolver::class)
            ->resolve(
                $botMan->getUser(),
                $botMan->getDriver()->getName(),
                $this->botModel->seller_id
            );

        if (!isset($client->order)) {
            return false;
        }

        if ($client->order->wallet->getWalletType()->getValue() === WalletType::TYPE_KUNA_CODE) {
            app()->make(KunaCodeHandler::class)->execute(
                $botMan,
                $this->botModel, ...
                $params
            );

            return true;
        }

        if ($client->order->wallet->getWalletType()->getValue() === WalletType::TYPE_GLOBAL_MONEY) {
            app()->make(GlobalMoneyCodeHandler::class)->execute(
                $botMan,
                $this->botModel, ...
                $params
            );

            return true;
        }

        if ($client->order->wallet->getWalletType()->getValue() === WalletType::TYPE_EASY_PAY) {
            app()->make(EasyPayCodeHandler::class)->execute(
                $botMan,
                $this->botModel,
                ...$params
            );

            return true;
        }

        app()->make(OrderCheckHandler::class)->execute($botMan, $this->botModel);

        return true;
    }

    /**
     * @param BotMan $botMan
     *
     * @throws BindingResolutionException
     */
    private function clearPreOrder(BotMan $botMan): void
    {
        $client = app()->make(ClientResolver::class)
            ->resolve(
                $botMan->getUser(),
                $botMan->getDriver()->getName(),
                $this->botModel->seller_id
            );
        if (!$client) {
            return;
        }

        $client->pre_order = null;
        $client->save();
    }

    /**
     * @param $message
     * @param array $recipients
     * @param string $driver
     *
     * @return Response
     * @return Response
     * @throws BotManException
     *
     */
    public function say($message, array $recipients, string $driver): Response
    {
        return $this->botman->say(
            $message,
            $recipients,
            $driver,
            ['parse_mode' => 'HTML']
        );
    }

    public function getBotman(): BotMan
    {
        return $this->botman;
    }

    /**
     * @param string $message
     * @param array $recipients
     * @param string $driver
     *
     * @return Response
     * @throws BotManException
     * @throws \JsonException
     *
     */
    public function saySuccessOrder(string $message, array $recipients, string $driver): Response
    {
        return $this->botman->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $message),
            $recipients,
            $driver,
            [
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => 'Наход'],
                            ['text' => 'Не наход'],
                        ],
                    ],
                ], JSON_THROW_ON_ERROR),
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\OrderCheckTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\VO\WalletType;
use BotMan\BotMan\BotMan;

/**
 * Class OrderCheckHandler.
 */
final class OrderCheckHandler implements BotHandler
{
    private ClientResolver          $clientResolver;
    private OrderCheckTextGenerator $orderTextGenerator;
    private MenuHandler             $menuHandler;

    public function __construct(
        MenuHandler $menuHandler,
        ClientResolver $clientResolver,
        OrderCheckTextGenerator $orderTextGenerator
    ) {
        $this->clientResolver = $clientResolver;
        $this->orderTextGenerator = $orderTextGenerator;
        $this->menuHandler = $menuHandler;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%order_check%')
            ->with('templates')
            ->first();

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        /** @var Order $order */
        $order = $client->order;

        if ($order) {
            if ($order->status->getValue() === OrderStatus::STATUS_AWAITING_PAYMENT) {
                $this->handleAwaitingPayment($botMan, $command, $order);

                $this->handleEnterCode($botMan, $order);

                return;
            }

            if ($order->status->getValue() === OrderStatus::STATUS_PARTIALLY_PAID) {
                $this->handlePartiallyPayment($botMan, $command, $order);

                $this->handleEnterCode($botMan, $order);

                return;
            }
        }

        $this->menuHandler->execute($botMan, $botModel);
    }

    private function handleAwaitingPayment(BotMan $botMan, BotLogicCommand $command, Order $order): void
    {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->orderTextGenerator->getAwaitingPaymentText($command, $order)
            ),
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * @param BotMan $botMan
     * @param Order  $order
     */
    private function handleEnterCode(BotMan $botMan, Order $order): void
    {
        switch ($order->wallet_type) {
            case WalletType::TYPE_KUNA_CODE:
            case WalletType::TYPE_EASY_PAY:
            case WalletType::TYPE_GLOBAL_MONEY:
                $botMan->reply(
                    str_replace(
                        ['\\\\r\\\\n', '\\r\\n'],
                        "\n",
                        'Пожалуйста, введите код оплаты'
                    ),
                    ['parse_mode' => 'HTML']
                );
        }
    }

    private function handlePartiallyPayment(BotMan $botMan, BotLogicCommand $command, Order $order): void
    {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->orderTextGenerator->getPartiallyPaidText($command, $order)
            ),
            ['parse_mode' => 'HTML']
        );
    }
}

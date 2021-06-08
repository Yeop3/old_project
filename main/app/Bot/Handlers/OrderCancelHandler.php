<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\OrderCancelTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Order\CancelOrderCommand;
use App\Services\Order\VO\CancelInitiator;
use App\Services\Order\VO\OrderStatus;
use BotMan\BotMan\BotMan;

/**
 * Class OrderCancelHandler.
 */
final class OrderCancelHandler implements BotHandler
{
    private ClientResolver           $clientResolver;
    private MenuHandler              $menuHandler;
    private OrderCancelTextGenerator $cancelTextGenerator;
    private CancelOrderCommand       $cancelOrderCommand;

    public function __construct(
        MenuHandler $menuHandler,
        ClientResolver $clientResolver,
        OrderCancelTextGenerator $cancelTextGenerator,
        CancelOrderCommand $cancelOrderCommand
    ) {
        $this->clientResolver = $clientResolver;
        $this->menuHandler = $menuHandler;
        $this->cancelTextGenerator = $cancelTextGenerator;
        $this->cancelOrderCommand = $cancelOrderCommand;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%order_cancel%')
            ->with('templates')
            ->firstOrFail();

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        /** @var Order $order */
        $currentOrder = $client->order;

        if (!$currentOrder) {
            $this->menuHandler->execute($botMan, $botModel);

            return;
        }

        /** @var Order $order */
        $order = $client->orderAny;

        if ($order->status->getValue() !== OrderStatus::STATUS_AWAITING_PAYMENT) {
            $this->handleCancelDenied($botMan, $command);

            return;
        }

        $this->handleCancelOk($botMan, $command, $order);
    }

    private function handleCancelDenied(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'order_cancel_denied')->first();

        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $template->content
            ),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleCancelOk(BotMan $botMan, BotLogicCommand $command, Order $order): void
    {
        $this->cancelOrderCommand->execute($order, new CancelInitiator(CancelInitiator::INITIATOR_CLIENT));

        $template = $command->templates->where('key', 'order_cancel_success')->first();

        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $template->content
            ),
            ['parse_mode' => 'HTML']
        );
    }
}

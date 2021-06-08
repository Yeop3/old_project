<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Bot\TextGenerators\OrderLastTextGenerator;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\BotLogic\BotLogicCommandTemplate;
use App\Models\Order;
use App\Services\Client\ClientResolver;
use App\Services\Order\VO\OrderStatus;
use BotMan\BotMan\BotMan;

/**
 * Class OrderLastHandler.
 */
final class OrderLastHandler
{
    private ClientResolver         $clientResolver;
    private OrderLastTextGenerator $orderLastTextGenerator;

    /**
     * OrderLastHandler constructor.
     *
     * @param ClientResolver         $clientResolver
     * @param OrderLastTextGenerator $orderLastTextGenerator
     */
    public function __construct(ClientResolver $clientResolver, OrderLastTextGenerator $orderLastTextGenerator)
    {
        $this->clientResolver = $clientResolver;
        $this->orderLastTextGenerator = $orderLastTextGenerator;
    }

    /**
     * @param BotMan      $botMan
     * @param Bot         $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;
        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%last_order%')
            ->with('templates')
            ->first();
        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        $order = $client->orders()
            ->whereIn('status', [
                OrderStatus::STATUS_PAID,
                OrderStatus::STATUS_GIVEN,
            ])
            ->orderByDesc('id')
            ->first();

        if (!$order) {
            $this->handleLastOrderAbsent($botMan, $command);

            return;
        }

        $this->handleLastOrder($botMan, $command, $order);
    }

    private function handleLastOrderAbsent(BotMan $botMan, BotLogicCommand $command): void
    {
        /* @var BotLogicCommandTemplate $template */
        $template = $command->templates->where('key', 'last_order_absent')->first();
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $template->content
            ),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleLastOrder(BotMan $botMan, BotLogicCommand $command, Order $order): void
    {
        /* @var BotLogicCommandTemplate $template */
        $template = $command->templates->where('key', 'last_order')->first();
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->orderLastTextGenerator->getLastOrderText($order, $template)
            ),
            ['parse_mode' => 'HTML']
        );
    }
}

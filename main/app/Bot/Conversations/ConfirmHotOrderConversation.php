<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Models\Order;
use App\Services\Order\ConfirmHotOrderCommand;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

/**
 * Class ConfirmHotOrderConversation.
 */
final class ConfirmHotOrderConversation extends Conversation
{
    public ?Order $order = null;

    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->order->refresh();
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->init();
    }

    private function init(): void
    {
        $this->askForAction();
    }

    private function askForAction(): void
    {
        $this->order = Order::find($this->order->id);

        $question = Question::create('Выберите действие')
            ->addButtons([
                Button::create('Подтвердить')->value('confirm'),
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if ($answer->getText() === 'confirm') {
                app()->make(ConfirmHotOrderCommand::class)->execute($this->order);

                $this->say('Подтверждено');

                return;
            }
        });
    }
}

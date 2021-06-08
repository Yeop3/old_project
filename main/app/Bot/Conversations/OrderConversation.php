<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Models\Order;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

/**
 * Class OrderConversation.
 */
final class OrderConversation extends Conversation
{
    protected int   $rating;
    protected Order $order;
    protected array $ratings;

    /**
     * OrderConversation constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->rating = 0;
        $this->order = $order;

        $this->ratings = [];
        for ($i = 0; $i < 5; $i++) {
            $this->ratings[] = Button::create($i + 1)->value($i + 1);
        }
    }

    /**
     * @return mixed|void
     */
    public function run()
    {
        $this->askRating();
    }

    private function askRating(): void
    {
        $question = Question::create('Оцените качество от 1 до 5')
            ->addButtons($this->ratings);

        $this->ask($question, function (Answer $answer) {
            $rating = $answer->getText();

            if ($this->checkRating($rating)) {
                return;
            }

            $this->rating = (int) $rating;

            $this->order->rating = $this->rating;
            $this->order->save();

            $this->askComment();
        });
    }

    /**
     * @param $rating
     *
     * @return bool
     */
    private function checkRating($rating): bool
    {
        return !is_numeric($rating) && ($rating >= 0 && $rating <= 5);
    }

    private function askComment(): void
    {
        $question = Question::create('Оставьте ваш комментарий');

        $this->ask($question, function (Answer $answer) {
            $this->order->client_comment = $answer->getText();
            $this->order->save();
            $this->say('Спасибо за отзыв');
        });
    }
}

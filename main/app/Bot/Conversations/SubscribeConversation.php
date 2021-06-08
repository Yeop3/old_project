<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\TelegramDriver;
use Modules\Users\Entities\User;
use TheArdent\Drivers\Viber\ViberDriver;

/**
 * Class SubscribeConversation.
 */
class SubscribeConversation extends Conversation
{
    protected $user;
    protected $bot_user;

    /**
     * SubscribeConversation constructor.
     *
     * @param User $user
     * @param null $bot_user
     */
    public function __construct(User $user, $bot_user = null)
    {
        $this->user = $user;
        $this->bot_user = $bot_user;
    }

    /**
     * @return mixed|void
     */
    public function run()
    {
        $this->askConfirm();
    }

    /**
     * @return SubscribeConversation
     */
    public function askConfirm(): SubscribeConversation
    {
        $companyName = $this->user->company->name;

        $text = __("orders::bot.start.{$this->user->role_id}", array_merge(
            $this->user->only(['name']),
            ['company_name' => $companyName]
        ));

        $question = Question::create($text)
            ->addButtons([
                Button::create('Подписаться')->value('yes'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            $bot = $this->getBot();

            $key = null;

            switch ($bot->getDriver()->getName()) {
                case TelegramDriver::DRIVER_NAME:
                    $key = 'telegram_id';
                    break;
                case ViberDriver::DRIVER_NAME:
                    $key = 'viber_id';
                    break;
            }

            $id = $answer->getMessage()->getSender();

            if ($key) {
                if (!User::where($key, $id)->exists()) {
                    $this->user->update([
                        $key => $id,
                    ]);

                    $this->say(__('orders::bot.after_subscribing'));
                } else {
                    $this->say(__('orders::bot.fail_subscribing'));
                }
            }
        });
    }
}

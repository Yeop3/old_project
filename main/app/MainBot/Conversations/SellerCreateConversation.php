<?php

namespace App\MainBot\Conversations;

use App\Services\Seller\Create\CreateSellerCommand;
use App\Services\Seller\Create\CreateSellerDto;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

/**
 * Class SellerCreateConversation.
 */
class SellerCreateConversation extends Conversation
{
    protected $name;
    protected $password_bot;
    protected $domain;
    protected $password_enter;
    protected $password_enter_confirm;

    public function askPasswordBot(): void
    {
        $this->say('Для отмены введите /cancel');

        $this->ask('Введите пароль', function (Answer $answer) {
            if ($answer->getText() === '/cancel') {
                $this->say('Регистрация отменена');

                return false;
            }

            $this->password_bot = $answer->getText();

            if (bcrypt($this->password_bot) !== config('bots.telegram.test_main_seller_create_password')) {
                $this->say('В доступе отказано');

                return false;
            }

            $this->askName();
        });
    }

    public function askName(): void
    {
        $this->say('Для отмены введите /cancel');

        $this->ask('Введите название', function (Answer $answer) {
            if ($answer->getText() === '/cancel') {
                $this->say('Регистрация отменена');

                return false;
            }

            $this->name = $answer->getText();

            $this->askDomain();
        });
    }

    public function askDomain(): void
    {
        $this->say('Для отмены введите /cancel');

        $this->ask('Введите домен', function (Answer $answer) {
            if ($answer->getText() === '/cancel') {
                $this->say('Регистрация отменена');

                return false;
            }

            $this->domain = $answer->getText();

            $this->askPasswordEnter();
        });
    }

    public function askPasswordEnter(): void
    {
        $this->say('Для отмены введите /cancel');

        $this->ask('Введите пароль для входа', function (Answer $answer) {
            if ($answer->getText() === '/cancel') {
                $this->say('Регистрация отменена');

                return false;
            }

            $this->password_enter = $answer->getText();

            $this->askPasswordEnterConfirm();
        });
    }

    public function askPasswordEnterConfirm(): void
    {
        $this->say('Для отмены введите /cancel');

        $this->ask('Подтвертиде пароль для входа', function (Answer $answer) {
            if ($answer->getText() === '/cancel') {
                $this->say('Регистрация отменена');

                return false;
            }

            $this->password_enter_confirm = $answer->getText();

            if ($this->password_enter_confirm !== $this->password_enter) {
                $this->say('Пароль неверный');

                return false;
            }

            app()->make(CreateSellerCommand::class)->execute(new CreateSellerDto($this->name, $this->domain, $this->password_enter));

            $this->say('Продавец '.$this->name.' на домене '.$this->domain.' создан');
        });
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->askPasswordBot();
    }
}

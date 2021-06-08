<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Bot\Commands\SendOrderPhotos;
use App\Http\Requests\Product\ProductRules;
use App\Models\Driver;
use App\Models\Order;
use App\Services\Product\Checker;
use App\Services\Product\SendProductByTaxiCommand;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use GuzzleHttp\Client;
use stdClass;

/**
 * Class ProcessTaxiOrderConversation.
 */
final class ProcessTaxiOrderConversation extends Conversation
{
    public stdClass $values;
    public Order    $order;
    public Driver   $driver;
    public Checker  $checker;

    public function __construct(Checker $checker, Order $order, Driver $driver)
    {
        $this->values = new stdClass();
        $this->order = $order;
        $this->driver = $driver;

        $this->order->refresh();
        $this->checker = $checker;
    }

    /**
     * @return mixed|void
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
        $sendButton = [];
        $photoButton = [];
        $showPhotoButton = [];
        $this->order = Order::find($this->order->id);

        $photosCount = $this->order->product->photos->count();

        if ($photosCount > 0) {
            $sendButton[] = Button::create('Отправить')->value('send');
            $showPhotoButton[] = Button::create('Показать загруженные фото')->value('show_photos');
        }

        if ($photosCount < 5) {
            $photoButton[] = Button::create('Загрузить фото')->value('upload_photo');
        }

        $text = "Загружено фотографий: $photosCount\n";
        $text .= 'Описание: '.($this->order->product->address ?? 'Не указано')."\n";
        $text .= 'Выберите действие';

        $addressButtonText = $this->order->product->address
            ? 'Изменить описание'
            : 'Заполнить описание';

        $question = Question::create($text)
            ->addButtons([
                ...$photoButton,
                ...$showPhotoButton,
                Button::create($addressButtonText)->value('set_address'),
                ...$sendButton,
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if ($answer->getText() === 'send' && $this->order->product->photos->count() > 0) {
                app()->make(SendProductByTaxiCommand::class)->execute($this->order);

                return;
            }

            if ($answer->getText() === 'upload_photo' && $this->order->product->photos->count() < 5) {
                $this->askPhotos();

                return;
            }

            if ($answer->getText() === 'show_photos' && $this->order->product->photos->count() > 0) {
                app()->make(SendOrderPhotos::class)
                    ->execute(new Client(), $this->order->source, $this->order, '');

                $this->askForAction();

                return;
            }

            if ($answer->getText() === 'set_address') {
                $this->askAddress();

                return;
            }
        });
    }

    private function askPhotos(): void
    {
        $question = Question::create('Прикрепите фото. Можно прикреплять по одному фото за раз.')
            ->addButtons([
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->askForImages($question, function ($images) {
            $this->checker->storeImages($this->order->product, [new ImageObject($images[0]->getUrl())]);

            $this->askPhotosAgain();
        }, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            $this->askPhotos();
        });
    }

    private function askPhotosAgain(): void
    {
        $question = Question::create('Прикрепите еще одно фото, или нажмите Продолжить.')
            ->addButtons([
                Button::create('Продолжить')->value('photo_continue'),
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->askForImages($question, function ($images) {
            if ($this->order->product->photos->count() >= 5) {
                $this->say('Максимум можно прикрепить 5 фото.');

                return;
            }

            $this->checker->storeImages($this->order->product, [new ImageObject($images[0]->getUrl())]);

            $this->askPhotosAgain();
        }, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if ($answer->isInteractiveMessageReply() && $answer->getValue() === 'photo_continue') {
                $this->askForAction();

                return;
            }

            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            $this->askPhotosAgain();
        });
    }

    /**
     * Ask description.
     */
    private function askAddress(): void
    {
        $rules = ProductRules::address();
        $question = Question::create('Введите описание')
            ->addButtons(ConversationHelpers::createButtons([], $rules));

        $this->ask($question, function (Answer $answer) use ($rules) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if (ConversationHelpers::isSkiped($answer)) {
                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, $rules);

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askAddress();

                return;
            }

            $this->checker->setAddress($this->order->product, $value);

            $this->askForAction();
        });
    }
}

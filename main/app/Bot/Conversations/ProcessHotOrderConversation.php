<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Bot\Commands\SendOrderPhotos;
use App\Http\Requests\Product\ProductRules;
use App\Models\Driver;
use App\Models\Order;
use App\Services\Coordinates\Coordinates;
use App\Services\Order\ConfirmHotOrderCommand;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\Checker;
use App\Services\Product\SendHotProductCommand;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use GuzzleHttp\Client;
use stdClass;
use Throwable;

/**
 * Class ProcessHotOrderConversation.
 */
final class ProcessHotOrderConversation extends Conversation
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
        $confirmButton = [];
        $sendButton = [];
        $photoButton = [];
        $showPhotoButton = [];
        $this->order = Order::find($this->order->id);

        $photosCount = $this->order->product->photos->count();

        if ($this->order->status->getValue() === OrderStatus::STATUS_PAID) {
            $confirmButton[] = Button::create('Подтвердить')->value('confirm');
        }

        if ($photosCount > 0 && $this->order->product->coordinates) {
            $sendButton[] = Button::create('Отправить')->value('send');
        }

        if ($photosCount < 5) {
            $photoButton[] = Button::create('Загрузить фото')->value('upload_photo');
        }

        if ($photosCount > 0) {
            $showPhotoButton[] = Button::create('Показать загруженные фото')->value('show_photos');
        }

        $text = "Загружено фотографий: $photosCount\n";
        $text .= 'Координаты: '.($this->order->product->coordinates ?? 'Не указаны')."\n";
        $text .= 'Описание: '.($this->order->product->address ?? 'Не указано')."\n";
        $text .= 'Выберите действие';

        $coordinatesButtonText = $this->order->product->coordinates
            ? 'Изменить координаты'
            : 'Заполнить координаты';

        $addressButtonText = $this->order->product->address
            ? 'Изменить описание'
            : 'Заполнить описание';

        $question = Question::create($text)
            ->addButtons([
                ...$confirmButton,
                ...$sendButton,
                ...$photoButton,
                ...$showPhotoButton,
                Button::create($coordinatesButtonText)->value('set_coordinates'),
                Button::create($addressButtonText)->value('set_address'),
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if ($answer->getText() === 'confirm') {
                app()->make(ConfirmHotOrderCommand::class)->execute($this->order);

                return;
            }

            if ($answer->getText() === 'send' && $this->order->product->photos->count() > 0) {
                app()->make(SendHotProductCommand::class)->execute($this->order);

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

            if ($answer->getText() === 'set_coordinates') {
                $this->askCoordinates();

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

    private function askCoordinates(): void
    {
        $rules = ProductRules::coordinates($this->order->seller_id);
        $question = Question::create('Прикрепите геопозицию или введите координаты. Например: 23.222222, 21.000000')
            ->addButtons([
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        try {
            $this->askForLocation($question, function (Location $location) {
                $coordinates = $location->getLatitude().','.$location->getLongitude();

                $this->checker->setCoordinates($this->order->product, new Coordinates($coordinates));

                $this->askForAction();
            }, function (Answer $answer) use ($rules) {
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
                    $this->askCoordinates();

                    return;
                }

                $this->checker->setCoordinates($this->order->product, new Coordinates($value));

                $this->askForAction();
            });
        } catch (Throwable $th) {
        }
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

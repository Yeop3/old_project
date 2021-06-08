<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Http\Requests\Product\ProductRules;
use App\Models\Bot;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Seller;
use App\Services\Product\Create\CreateProductCommand;
use App\Services\Product\ProductDto;
use App\Services\Product\ProductDtoSetter;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use App\VO\Commission;
use App\VO\CommissionType;
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location as BotLocation;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\QueryBuilder;
use Throwable;

/**
 * Class CreateProductConversation.
 */
final class CreateProductConversation extends Conversation {

    public const CANCEL_MESSAGE = 'Создание отменено.';

    public Seller                             $seller;
    public CreateProductCommand               $command;
    public ProductDtoSetter                   $productDto;
    public Driver                             $driver;
    /**
     * @var string
     */
    public string               $productMessage;
    public Collection           $firstBotMessage;
    public IncomingMessage      $lastUserMessage;
    /**
     * @var Bot
     */
    public Bot $botModel;

    /**
     * CreateProductConversation constructor.
     *
     * @param Seller $seller
     * @param Bot $botModel
     * @param Driver $driver
     * @param ProductDtoSetter $dto
     * @throws BindingResolutionException
     */
    public function __construct(
        Seller $seller,
        Bot $botModel,
        Driver $driver,
        ProductDtoSetter $dto
    )
    {
        $this->seller     = $seller;
        $this->productDto = $dto;
        $this->command    = app()->make(CreateProductCommand::class);
        $this->driver     = $driver;
        $this->botModel   = $botModel;

    }

    /**
     * @return mixed|void
     */
    public function run()
    {
        $this->init();
    }

    public function init(): void
    {

        $this->productDto->setDriverNumber($this->driver->number);

        $this->productDto->setTelegramId((int)$this->bot->getUser()->getId());
        $this->askProductType();
    }

    public function askProductType(): void
    {
        $rules    = ProductRules::productTypeId($this->seller->id);
        $products = $this->seller->productTypes()
            ->where('delivery_type', DeliveryType::TREASURE)
            ->where('active', 1)
            ->pluck('name', 'number');


        $question = Question::create('Выберите товар')
            ->addButtons(ConversationHelpers::createButtons($products, $rules));


        $this->ask($question,
            function (Answer $answer) use ($rules): void {


                $this->firstBotMessage = collect($answer->getMessage()->getPayload());

                if (ConversationHelpers::isCancel($answer)) {

                    //TODO: MAKE UPDATE MESSAGE
                    return;
                }

                if (ConversationHelpers::isSkiped($answer)) {
                    $this->askLocation();

                    return;
                }

                //$this->say('Message Payload:' .
                //  json_encode($answer->getMessage()->getPayload(), JSON_UNESCAPED_UNICODE));
//                $this->say('Message Extras'.
//                    var_export($answer->getMessage()->getExtras()));

                //$this->firstBotMessage = $answer->getMessage()->getExtras();

                $value = $answer->getText();

                $validator = valueValidator($value, $rules);

                if ($validator->fails()) {
                    $this->say(valueValidatorError($validator));

                    $this->askProductType();

                    return;
                }


                $this->productDto->setProductTypeNumber((int)$value);
                $this->productMessage = 'Тип Продукта:' . $value;
                $this->updateFirstMessage($this->productMessage);

                $this->askLocation();
            },
        );
        //$this->getBot()->reply('Ask Data:' . json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    public function askLocation(): void
    {
        $locations = [];

        $driverLocations = $this->driver
            ->locations()
            ->pluck('id');

        $this->seller->locations()
            ->with('ancestors')
            ->whereDoesntHave('children')
            ->where(fn (QueryBuilder $builder) => $builder
                ->whereHas(
                    'ancestors',
                    fn ($builder) => $builder->whereIn('id', $driverLocations)
                )
                ->orWhereIn('id', $driverLocations)
            )
            ->get()
            ->each(function (Location $location) use (&$locations) {
                $locations[$location->number] = $location->name_chain;
            });


        //Log::error(json_encode($locations, JSON_UNESCAPED_UNICODE));


        //$this->say(json_encode($locations, JSON_UNESCAPED_UNICODE));


        $rules    = ProductRules::localtionId($this->seller->id);
        $question = Question::create('Вы можете добавить в городах: ')
            ->addButtons(ConversationHelpers::createButtons($locations, $rules));

        $this->ask(
            $question,
            function (Answer $answer) use ($rules): void {

                //$this->say($this->firstBotMessage);

                if (ConversationHelpers::isCancel($answer)) {
                    $this->say(self::CANCEL_MESSAGE);

                    return;
                }

                if (ConversationHelpers::isSkiped($answer)) {
                    $this->askCoordinates();

                    return;
                }

                $value = $answer->getText();

                $validator = valueValidator($value, $rules);

                if ($validator->fails()) {
                    // $messageId = $answer->getMessage()->getPayload()['message_id'];

                    //$this->deleteMessage($messageId);

                    $this->say(valueValidatorError($validator));

                    $this->askLocation();

                    return;
                }

                $this->productDto->setStatus(
                    new ProductStatus(ProductStatus::STATUS_NOT_ACTIVE)
                );
                $this->productDto->setCommission(
                    new Commission(
                        0,
                        new CommissionType(
                            CommissionType::TYPE_FIXED
                        )
                    )
                );

                $this->productDto->setLocationNumber((int)$value);

                $this->productMessage .= "\n" . 'Локация:' . $value;
                $this->updateFirstMessage($this->productMessage);
                $this->askCoordinates();
            }
        );
    }

    public function askCoordinates(): void
    {
        $rules    = ProductRules::coordinates($this->seller->id);
        $question = Question::create(
            'Прикрепите геопозицию или введите координаты. Например: 23.222222, 21.000000'
        )->addButtons(ConversationHelpers::createButtons([], $rules));

        $this->askForLocation(
            $question,
            function (BotLocation $location): void {
                $coordinates = $location->getLatitude() . ',' . $location->getLongitude();
                $this->productDto->setCoordinates($coordinates);

                $this->productMessage .= "\n" . 'Координаты: ' . $coordinates;
                $this->updateFirstMessage($this->productMessage);
                $this->askPhotos();
            },
            function (Answer $answer) use ($rules): void {
                if (ConversationHelpers::isCancel($answer)) {

                    return;
                }

                if (ConversationHelpers::isSkiped($answer)) {
                    $this->askPhotos();

                    return;
                }

                $value = $answer->getText();

                $validator = valueValidator($value, $rules);

                if ($validator->fails()) {
                    $this->say(valueValidatorError($validator));
                    $this->askCoordinates();

                    return;
                }

                $this->productDto->setCoordinates($value);

                $this->productMessage .= "\n" . 'Координаты: ' . $value;
                $this->updateFirstMessage($this->productMessage);
                $this->askPhotos();
            }
        );
    }

    public function askPhotos(): void
    {
        // TODO вы прикрепили столько то, осталось столько то
        // TODO описание перед видео
        // TODO координаты принимать напрямую как то

        $photoCount = count($this->productDto->getImages() ?? []);

        $text = $photoCount >= 1
            ? 'Прикрепили ' . $photoCount . '. Еще можно ' . (5 - $photoCount)
            : 'Загрузите фото максимум 5 штук.';


        $continueButton = [];

        if ($photoCount > 0) {
            $continueButton[] = Button::create('Продолжить')->value('photo_continue');
        }

        $question = Question::create($text)
            ->addButtons([
                ...$continueButton,
                ...ConversationHelpers::createButtons(),
            ]);

        //Get First Callback
        $getAlbum = function ($images) {

            $imagesData = collect($images)
                ->map(fn (Image $image): ImageObject => new ImageObject($image->getUrl()))
                ->toArray();
//
//            $this->productDto->setImages([
//                ...$this->productDto->getImages() ?? [],
//                ...$imagesData,
//            ]);


//            if (count($this->productDto->getImages() ?? []) >= 5) {
//                $this->askVideo();
//
//                return;
//            }

           return 'That is False';
            //$this->say(json_encode($imagesData));

            //            if($imagesData){
//                $this->askPhotos();
//            }


        };

        //Get Second Callback
        $getRepeat = function (Answer $answer) {
//            if (count($this->productDto->getImages() ?? []) >= 5) {
////                $this->say('Максимум можно прикрепить 5 фото.');
//                $this->askVideo();
//
//                return; //TODO: 4toto vernut;
//            }
//
//            if ($answer->isInteractiveMessageReply() && $answer->getValue() === 'photo_continue') {
//                $this->askVideo();
//
//                return;
//            }
//
//            if (ConversationHelpers::isCancel($answer)) {
//                $this->getBot()->reply(self::CANCEL_MESSAGE);
//
//                return;
//            }
            $this->say(json_encode(
                    $answer->getMessage()->getExtras()) .
                json_encode($answer->getMessage()->getPayload(),
                    JSON_UNESCAPED_UNICODE)
            );

            $this->askVideo();
        };

        $this->askForImages(
            $question,
            $getAlbum,
            $getRepeat,
        );
    }

    public function askVideo(): void
    {
        $rules    = ProductRules::video();
        $question = Question::create('Прикрепите видео')
            ->addButtons(ConversationHelpers::createButtons([], $rules));

        $this->askForVideos(
            $question,
            function ($videos) {
                $this->productDto->setVideo(ConversationHelpers::saveTempFile($videos[0]->getUrl()));

                $this->askAddress();
            },
            function (Answer $answer) {
                if (ConversationHelpers::isCancel($answer)) {
                   // $this->updateLastMessage(self::CANCEL_MESSAGE);

                    return;
                }

                if (ConversationHelpers::isSkiped($answer)) {
                    $this->askAddress();

                    return;
                }

                $this->askVideo();
            }
        );
    }

    /**
     * Ask description.
     */
    public function askAddress(): void
    {
        $rules    = ProductRules::address();
        $question = Question::create('Введите описание')
            ->addButtons(ConversationHelpers::createButtons([], $rules));

        $this->ask(
            $question,
            function (Answer $answer) use ($rules) {
                if (ConversationHelpers::isCancel($answer)) {
                    //$this->updateLastMessage(self::CANCEL_MESSAGE);

                    return;
                }

                if (ConversationHelpers::isSkiped($answer)) {
                    $this->createProduct();

                    return;
                }

                $value = $answer->getText();

                $validator = valueValidator($value, $rules);

                if ($validator->fails()) {
                    $this->say(valueValidatorError($validator));
                    $this->askAddress();

                    return;
                }

                $this->productDto->setAddress($value);
                $this->createProduct();
            }
        );
    }

    /**
     * Create product in system by TG.
     *
     * @throws Throwable
     */
    public function createProduct(): void
    {
        $videoPath = $this->productDto->getVideo();
        $video     = $videoPath ? new UploadedFile(
            storage_path('app/') . $videoPath,
            'video'
        ) : null;

        $dto = new ProductDto(
            $this->productDto->getDriverNumber(),
            $this->productDto->getProductTypeNumber(),
            $this->productDto->getLocationNumber(),
            $this->productDto->getCommission(),
            $this->productDto->getCoordinates(),
            array_slice($this->productDto->getImages() ?? [], 0, 5),
            new ProductStatus($this->productDto->getStatus()->getValue()),
            $video,
            $this->productDto->getAddress(),
            null,
            $this->productDto->getTelegramId()
        );

        $this->command->execute($this->seller, $dto);

        //$this->updateLastMessage('Продукт добавлен.');

        ConversationHelpers::removeTempFile($videoPath);
    }



    /**
     * @param string $text
     * @throws BadMethodCallException
     */
    public function updateFirstMessage(string $text): void
    {


        /**
         * Message Payload:
         *                {
         * "message_id":36565,
         * "from":{
         * "id":1233324512,
         * "is_bot":true,
         * "first_name":"🍊CITRUS🍊",
         * "username":"CitrusMega_bot"
         * },
         * "chat":{
         * "id":1345774735,
         * "first_name":"Gayniger",
         * "last_name":"Gayniger",
         * "username":"supergaynigger228_blya",
         * "type":"private"
         * },
         * "date":1603700047,
         * "text":"Выберите товар",
         * "reply_markup":{
         * "inline_keyboard":[
         * [
         * {
         * "text":"Шишки",
         * "callback_data":"5"
         * }
         * ],
         * [
         * {
         * "text":"Альфа PVP",
         * "callback_data":"6"
         * }
         * ],
         * [
         * {
         * "text":"Таблы (E)",
         * "callback_data":"7"
         * }
         * ],
         * [
         * {
         * "text":"Меф",
         * "callback_data":"8"
         * }
         * ],
         * [
         * {
         * "text":"Амф",
         * "callback_data":"9"
         * }
         * ],
         * [
         * {
         * "text":"Отменить создание",
         * "callback_data":"cancel"
         * }
         * ]
         * ]
         * }
         * }**/


        $this->bot->sendRequest('editMessageText', [
            'chat_id'    => $this->firstBotMessage->get('chat')['id'],
            'message_id' => $this->firstBotMessage->get('message_id'),
            'text'       => $text,
        ]);

    }

    protected function getFirstMessageId(): ?int
    {
//        $telegramId = $this->bot->getUser()->getId();
//
//        $id = Cache::store('file')->get("conversation:create_product:$telegramId:first_message_id");
//
//        if (!$id) {
//            return null;
//        }
//
//        return (int) $id;
    }

}

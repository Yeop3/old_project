<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Http\Requests\ProductType\ProductTypeRules;
use App\Models\Seller;
use App\Services\ProductType\CreateProductTypeCommand;
use App\Services\ProductType\ProductTypeDto;
use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Commission;
use App\VO\CommissionType;
use App\VO\Unit;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Money\Currency;
use Money\Money;
use stdClass;

/**
 * Class CreateProductTypeConversation.
 */
final class CreateProductTypeConversation extends Conversation
{
    protected const CANCEL_MESSAGE = 'Создание товара отменено.';
    protected Seller                   $seller;
    protected createProductTypeCommand $createProductTypeCommand;
    protected stdClass                 $values;

    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
        $this->values = new stdClass();
        $this->createProductTypeCommand = app()->make(CreateProductTypeCommand::class);
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
        $question = Question::create('Введите имя товара')
            ->addButtons(ConversationHelpers::createButtons());

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, ProductTypeRules::name($this->seller->id), 'name');

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->init();

                return;
            }

            $this->values->name = $value;
            $this->askPrice();
        });
    }

    private function askPrice(): void
    {
        $question = Question::create('Введите цену. Например: 100')
            ->addButtons(ConversationHelpers::createButtons());

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, ProductTypeRules::price());

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askPrice();

                return;
            }

            $this->values->price = $value;
            $this->askTypeComission();
        });
    }

    private function askTypeComission(): void
    {
        $question = Question::create('Выберите тип комиссии')
            ->addButtons(ConversationHelpers::createButtons(CommissionType::TYPES));

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, ProductTypeRules::comissionType());

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askTypeComission();

                return;
            }

            $this->values->comissionType = $value;
            $this->askComission();
        });
    }

    private function askComission(): void
    {
        $question = Question::create('Введите комиссию. Например: 25')
            ->addButtons(ConversationHelpers::createButtons());

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator(
                $value,
                ProductTypeRules::comissionValue($this->values->comissionType),
                'commission_type'
            );

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askComission();

                return;
            }

            $this->values->comission = $value;
            $this->askUnit();
        });
    }

    private function askUnit(): void
    {
        $question = Question::create('Введите размерность')
            ->addButtons(ConversationHelpers::createButtons(Unit::UNITS));

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, ProductTypeRules::unit());

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askUnit();

                return;
            }

            $this->values->unit = $value;
            $this->askPacking();
        });
    }

    private function askPacking(): void
    {
        $question = Question::create('Введите количество')
            ->addButtons(ConversationHelpers::createButtons());

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                $this->say(self::CANCEL_MESSAGE);

                return;
            }

            $value = $answer->getText();

            $validator = valueValidator($value, ProductTypeRules::packing());

            if ($validator->fails()) {
                $this->say(valueValidatorError($validator));
                $this->askQuantity();

                return;
            }

            $this->values->packing = $value;
            $this->createProductType();
        });
    }

    private function createProductType(): void
    {
        $dto = new ProductTypeDto(
            $this->values->name,
            new Money((float) $this->values->price * 100, new Currency('UAH')),
            new Commission(
                (int) $this->values->comission ?: 0,
                new CommissionType((int) $this->values->comissionType)
            ),
            (int) $this->values->packing,
            new Unit((int) $this->values->unit),
            array_map(
                fn (string $paymentMethodValue) => new PaymentMethod((int) $paymentMethodValue),
                array_keys(PaymentMethod::TYPES)
            )
        );

        $this->createProductTypeCommand->execute($this->seller, $dto);

        $this->say('товар создан.');
    }
}

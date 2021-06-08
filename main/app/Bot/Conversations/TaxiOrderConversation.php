<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use App\Bot\TextGenerators\OrderTextGenerator;
use App\Jobs\ReminderOrderJob;
use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\BotLogic\BotLogicReminder;
use App\Models\Client;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\Order\CreateTaxiOrderCommand;
use App\Services\Order\CreateTaxiOrderDto;
use App\Services\Order\Exceptions\DriverForTaxiOrderNotFoundException;
use App\Services\Order\VO\CancelCodes;
use App\Services\Wallet\Exceptions\RotateWalletsAreBusyException;
use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Commission;
use App\VO\CommissionType;
use App\VO\Source;
use App\VO\SourceType;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Money\Money;
use stdClass;

/**
 * Class TaxiOrderConversation.
 */
final class TaxiOrderConversation extends Conversation
{
    public stdClass                $values;
    public ?Client                 $client = null;
    public ?ProductType            $productType = null;
    public ?Location               $location = null;
    public ?CreateTaxiOrderCommand $createTaxiOrderCommand = null;
    public Bot                     $botModel;
    public BotLogicCommand         $logicCommand;

    public function __construct(
        CreateTaxiOrderCommand $createTaxiOrderCommand,
        Bot $botModel,
        Client $client,
        ProductType $productType,
        Location $location
    ) {
        $this->values = new stdClass();
        $this->createTaxiOrderCommand = $createTaxiOrderCommand;
        $this->client = $client;
        $this->productType = $productType;
        $this->location = $location;

        $botLogic = $botModel->logic;

        /** @var BotLogicCommand $command */
        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%order_[Location:ID]%')
            ->with('templates')
            ->first();
        $this->logicCommand = $command;

        $this->botModel = $botModel;
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
        $this->askCount();
    }

    private function askCount(): void
    {
        $unitCase = $this->productType->unit->getCaseValue();

        $question = Question::create("Укажите, пожалуйста, сколько вам нужно ($unitCase).")
            ->addButtons([
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            $count = $answer->getText();
            $count = str_replace(',', '.', $count);

            if (!is_numeric($count)) {
                $this->say('Введите, пожалуйста, число');
                $this->askCount();

                return;
            }

            if ($count < 5) {
                $this->say("Минимальное количество - 5 {$this->productType->unit->getReadableValue()}");
                $this->askCount();

                return;
            }

            $this->values->count = round((float) $count, $this->productType->unit->getRoundPrecision());

            $this->askAgreeWithPrice();
        });
    }

    private function askAgreeWithPrice(): void
    {
        $tempProduct = new Product();
        $tempProduct->setRelation('productType', $this->productType);
        $tempProduct->setRelation('location', $this->location);
        $tempProduct->commission = new Commission(0, new CommissionType(CommissionType::TYPE_FIXED));
        $tempProduct->count = $this->values->count;

        /** @var Money $totalPrice */
        [, , , $totalPrice] = $this->createTaxiOrderCommand->getOrderCalculator()
            ->calc($tempProduct, $this->client);

        $totalPriceFormatted = formatMoney($totalPrice);

        $question = Question::create("Итоговая цена {$totalPriceFormatted} грн")
            ->addButtons([
                Button::create('Заказать')->value('agree'),
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            if ($answer->getText() !== 'agree') {
                return;
            }

            $this->askAddress();
        });
    }

    private function askAddress(): void
    {
        $question = Question::create('Укажите, пожалуйста, адрес.')
            ->addButtons([
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            $this->values->address = $answer->getText();

            $this->askPaymentMethod();
        });
    }

    private function askPaymentMethod(): void
    {
        $paymentMethods = $this->createTaxiOrderCommand->getPaymentMethods($this->client, $this->productType);

        if (!count($paymentMethods)) {
            $this->handleDoesntHavePaymentMethods();

            return;
        }

        $question = Question::create('Выберите способ оплаты')
            ->addButtons([
                ...array_map(
                    fn (
                        PaymentMethod $paymentMethod
                    ) => Button::create($paymentMethod->getReadableValue())->value($paymentMethod->getValue()),
                    $paymentMethods
                ),
                Button::create('Отмена')->value(ConversationHelpers::CANCEL_VALUE),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (ConversationHelpers::isCancel($answer)) {
                return;
            }

            $value = $answer->getText();

            $paymentMethod = (int) $value;

            try {
                $paymentMethod = new PaymentMethod($paymentMethod);
            } catch (InvalidArgumentException $e) {
                return;
            }

            $this->createOrder($paymentMethod);
        });
    }

    private function handleDoesntHavePaymentMethods(): void
    {
        $template = $this->logicCommand->templates->where('key', 'order_pay_method_absent')->first();

        $this->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function createOrder(PaymentMethod $paymentMethod): void
    {
        try {
            $order = $this->createTaxiOrderCommand->execute(
                $this->client,
                new CreateTaxiOrderDto(
                    $this->productType,
                    $this->location,
                    $paymentMethod,
                    new Source($this->botModel->id, new SourceType(SourceType::TYPE_BOT)),
                    $this->values->count,
                    $this->values->address,
                    ''
                )
            );
        } catch (RotateWalletsAreBusyException $e) {
            $this->handleRotateWalletsAreBusy();

            return;
        } catch (DriverForTaxiOrderNotFoundException $e) {
            $this->handleDriverNotFound();

            return;
        } catch (Exception $e) {
            $time = time();
            $this->handleError("{$this->client->seller_id}/{$this->client->id}/$time");

            Log::channel('taxi_orders')->error(
                json_encode([
                    $e->getMessage(),
                    $e->getTrace(),
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            return;
        }

        try {
            $this->handleOrderCreated($order);

            /* @var BotLogicReminder $botLogicReminder */
            $botLogicReminder = BotLogicReminder::where('key', 'payment')
                ->whereBotLogicId($this->botModel->logic_id)
                ->first();

            $options = collect($botLogicReminder->options);

            $intervals = $options->first(fn ($value) => $value['key'] === 'intervals')['value'];

            if ($intervals) {
                $intervals = explode(',', $intervals);
                ReminderOrderJob::dispatch($order, 0, $this->botModel->logic, count($intervals) - 1)
                    ->delay(now()->addMinutes((int) $intervals[0]));
            }
        } catch (Exception $e) {
            $time = time();
            $this->handleError("{$this->client->seller_id}/{$this->client->id}/$time");

            Log::channel('taxi_orders')->error(
                json_encode([
                    $e->getMessage(),
                    $e->getTrace(),
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            return;
        }
    }

    private function handleRotateWalletsAreBusy(): void
    {
        $template = $this->logicCommand->templates->where('key', 'order_wallet_busy')->first();

        $this->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleDriverNotFound(): void
    {
        $this->say(
            (new CancelCodes(CancelCodes::DRIVER_NOT_FOUNT))->getMessage(),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleError(string $error): void
    {
        $this->say(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                app()->make(OrderTextGenerator::class)->getErrorText($this->logicCommand, $error)
            ),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleOrderCreated(Order $order): void
    {
        $orderCreatedTemplateKey = $order->payment_method->getTemplateKey('order_created');

        $template = $this->logicCommand->templates->where('key', $orderCreatedTemplateKey)->first();

        $text = app()->make(OrderTextGenerator::class)->getOrderCreatedText($template, $order);
        cache(["client_{$order->client->id}_{$order->source_id}_frequent_requests" => 1]);

        $this->say(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }
}

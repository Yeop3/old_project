<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

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
use App\Services\Client\ClientResolver;
use App\Services\Location\Exceptions\LocationIsNotFinalException;
use App\Services\Order\CreateOrderCommand;
use App\Services\Order\CreateOrderDto;
use App\Services\Order\Exceptions\PaymentMethodNotFoundException;
use App\Services\Order\Exceptions\ProductForOrderNotFoundException;
use App\Services\Order\Exceptions\ShouldSpecifyPaymentMethodException;
use App\Services\Product\Exceptions\LocationNotFoundException;
use App\Services\Product\Exceptions\ProductTypeNotFoundException;
use App\Services\Wallet\Exceptions\RotateWalletsAreBusyException;
use App\VO\Source;
use App\VO\SourceType;
use BotMan\BotMan\BotMan;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderHandler.
 */
final class OrderHandler implements BotHandler
{
    private OrderTextGenerator $orderTextGenerator;
    private CreateOrderCommand $createOrderCommand;
    private ClientResolver     $clientResolver;

    public function __construct(
        CreateOrderCommand $createOrderCommand,
        ClientResolver $clientResolver,
        OrderTextGenerator $orderTextGenerator
    ) {
        $this->orderTextGenerator = $orderTextGenerator;
        $this->createOrderCommand = $createOrderCommand;
        $this->clientResolver = $clientResolver;
    }

    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        [$productTypeNumber, $locationNumber] = $params;
        $paymentMethod = $params[2] ?? null;

        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%order_[Location:ID]%')
            ->with('templates')
            ->first();

        $client = $this->clientResolver->resolve(
            $botMan->getUser(),
            $botMan->getDriver()->getName(),
            $botModel->seller_id
        );

        try {
            $order = $this->createOrderCommand->execute(
                $client,
                new CreateOrderDto(
                    (int) $productTypeNumber,
                    (int) $locationNumber,
                    $paymentMethod,
                    new Source($botModel->id, new SourceType(SourceType::TYPE_BOT))
                )
            );
        } catch (ProductTypeNotFoundException $e) {
            $command = BotLogicCommand::whereBotLogicId($botLogic->id)
                ->where('keys', 'like', '%/product_{ID}%')
                ->with('templates')
                ->first();
            $productCommand = $command;

            $this->handleProductTypeNotFound($botMan, $productCommand);

            return;
        } catch (LocationNotFoundException $e) {
            $locationCommand = BotLogicCommand::whereBotLogicId($botLogic->id)
                ->where('keys', 'like', '%/location_{ID}%')
                ->with('templates')
                ->first();

            $this->handleLocationNotFound($botMan, $locationCommand);

            return;
        } catch (LocationIsNotFinalException $e) {
            $this->handleSubLocations($botMan, $command, $client, $e->getProductType(), $e->getLocation());

            return;
        } catch (ProductForOrderNotFoundException $e) {
            $this->handleProductNotFound($botMan, $command, $e->getProductType(), $e->getLocation());

            return;
        } catch (ShouldSpecifyPaymentMethodException $e) {
            if (!count($e->getPaymentMethods())) {
                $this->handleDoesntHavePaymentMethods($botMan, $command);

                return;
            }

            $client->pre_order = [
                'product_type' => $e->getProductType()->number,
                'location'     => $e->getLocation()->number,
            ];
            $client->save();

            $this->handleSelectPaymentMethod(
                $botMan,
                $command,
                $e->getProductType(),
                $e->getLocation(),
                $e->getPaymentMethods()
            );

            return;
        } catch (PaymentMethodNotFoundException $e) {
            $this->handleDoesntHavePaymentMethods($botMan, $command);

            return;
        } catch (RotateWalletsAreBusyException $e) {
            $this->handleRotateWalletsAreBusy($botMan, $command);

            return;
        } catch (Exception $e) {
            $time = time();
            $this->handleError($botMan, $command, "$client->seller_id/$client->id/$time");

            Log::channel('orders')->error(
                json_encode(
                    [
                        $e->getMessage(),
                        $e->getTrace(),
                    ],
                    JSON_PRETTY_PRINT
                )
            );

            return;
        }

        try {
            $this->handleOrderCreated($botMan, $command, $order);

            /* @var BotLogicReminder $botLogicReminder */
            $botLogicReminder = BotLogicReminder::where('key', 'payment')
                ->whereBotLogicId($botLogic->id)
                ->first();

            $options = collect($botLogicReminder->options);

            $intervals = $options->first(fn ($value) => $value['key'] === 'intervals')['value'];

            if ($intervals) {
                $intervals = explode(',', $intervals);
                ReminderOrderJob::dispatch(
                    $order,
                    0,
                    $botLogic,
                    count($intervals) - 1
                )->delay(now()->addMinutes((int) $intervals[0]));
            }
        } catch (Exception $e) {
            $time = time();
            $this->handleError($botMan, $command, "$client->seller_id/$client->id/$time");

            Log::channel('orders')->error(
                json_encode(
                    [
                        $e->getMessage(),
                        $e->getTrace(),
                    ],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            return;
        }
    }

    private function handleProductTypeNotFound(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'product_location_not_found')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleLocationNotFound(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'location_absent')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleSubLocations(
        BotMan $botMan,
        BotLogicCommand $command,
        Client $client,
        ProductType $productType,
        Location $location
    ): void {
        $finalSubLocations = $location->descendants()->whereDoesntHave('children')->get();
        $productsExist = Product::whereProductTypeId($productType->id)
            ->whereIn('location_id', $finalSubLocations->pluck('id'))
            ->active()
            ->exists();

        if (!$productsExist) {
            $botMan->reply(
                str_replace(
                    ['\\\\r\\\\n', '\\r\\n'],
                    "\n",
                    $this->orderTextGenerator->getSubLocationsDoesntHaveProductText($command, $productType, $location)
                ),
                ['parse_mode' => 'HTML']
            );

            return;
        }

        $text = $this->orderTextGenerator->getSubLocationsListText($command, $client, $productType, $location);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleProductNotFound(
        BotMan $botMan,
        BotLogicCommand $command,
        ProductType $productType,
        Location $location
    ): void {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->orderTextGenerator->getProductNotFoundText($command, $productType, $location)
            ),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleDoesntHavePaymentMethods(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'order_pay_method_absent')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleSelectPaymentMethod(
        BotMan $botMan,
        BotLogicCommand $command,
        ProductType $productType,
        Location $location,
        array $paymentMethods
    ): void {
        $botMan->reply(
            str_replace(
                ['\\\\r\\\\n', '\\r\\n'],
                "\n",
                $this->orderTextGenerator->getPaymentMethodsListText($command, $productType, $location, $paymentMethods)
            ),
            [
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        ...$this->orderTextGenerator->getPaymentMethodsListButton($command, $paymentMethods),
                    ],
                ]),
            ]
        );
    }

    private function handleRotateWalletsAreBusy(BotMan $botMan, BotLogicCommand $command): void
    {
        $template = $command->templates->where('key', 'order_wallet_busy')->first();

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template->content),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleError(BotMan $botMan, BotLogicCommand $command, string $error): void
    {
        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $this->orderTextGenerator->getErrorText($command, $error)),
            ['parse_mode' => 'HTML']
        );
    }

    private function handleOrderCreated(BotMan $botMan, BotLogicCommand $command, Order $order): void
    {
        $orderCreatedTemplateKey = $order->payment_method->getTemplateKey('order_created');

        $template = $command->templates->where('key', $orderCreatedTemplateKey)->first();

        $text = $this->orderTextGenerator->getOrderCreatedText($template, $order);
        cache(["client_{$order->client->id}_frequent_requests" => 1]);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $text),
            ['parse_mode' => 'HTML']
        );
    }
}

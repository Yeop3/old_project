<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Client;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Shift;
use App\Services\Order\Exceptions\DriverForTaxiOrderNotFoundException;
use App\Services\Order\Exceptions\ShiftNotFoundException;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use App\VO\CommissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Money\Money;

/**
 * Class CreateHotOrderCommand.
 */
final class CreateHotOrderCommand
{
    private WalletResolver $walletResolver;
    private ProductResolver $productResolver;
    private OrderCalculator $orderCalculator;

    public function __construct(
        WalletResolver $walletResolver,
        ProductResolver $productResolver,
        OrderCalculator $orderCalculator
    ) {
        $this->walletResolver = $walletResolver;
        $this->productResolver = $productResolver;
        $this->orderCalculator = $orderCalculator;
    }

    public function execute(Client $client, CreateHotOrderDto $dto): Order
    {
        $driver = $this->resolveDriver($client, $dto->getLocation());

        if (!$driver || !$driver->client) {
            throw new DriverForTaxiOrderNotFoundException();
        }

        $product = new Product();
        $product->seller_id = $client->seller_id;
        $product->product_type_id = $dto->getProductType()->id;
        $product->location_id = $dto->getLocation()->id;
        $product->driver_id = $driver->id;
        $product->client_telegram_id = $driver->telegram_id;
        $product->commission_value = 0;
        $product->commission_type = CommissionType::TYPE_FIXED;
        $product->status = new ProductStatus(ProductStatus::STATUS_BOOKED);
        $product->delivery_type = DeliveryType::hotTreasure();
        $product->count = $dto->getCount();
        $product->booked_at = now();

        $shift = Shift::whereSellerId($client->seller_id)->current()->first();

        if (!$shift) {
            throw new ShiftNotFoundException("There isn't current shift");
        }

        $walletType = WalletType::createByPaymentMethod($dto->getPaymentMethod());

        $wallet = $this->walletResolver->resolve($client->seller, $dto->getPaymentMethod());

        $order = new Order();

        $order->seller_id = $client->seller_id;
        $order->client_id = $client->id;
        $order->shift_id = $shift->id;
        $order->wallet_id = $wallet->id;
        $order->wallet_type = $walletType->getModelMorphClass();
        $order->payment_method = $dto->getPaymentMethod();
        $order->source_id = $dto->getSource()->getId();
        $order->source_type = $dto->getSource()->getType()->getValue();
        $order->status = new OrderStatus(OrderStatus::STATUS_AWAITING_PAYMENT);

        /** @var Money $totalPrice */
        [$order->commission, $discountItem, $order->discount_amount, $totalPrice] = $this->orderCalculator->calc($product, $client);

        $order->discount_id = $discountItem->getDiscountId();

        $order->price = $product->productType->price;
        $order->total_price = $totalPrice;
        $order->unpaid_amount = $totalPrice;

        $order->name = $product->productType->name;
        $order->packing = $product->productType->packing;
        $order->unit = $product->productType->unit ? $product->productType->unit->getReadableValue() : null;

        $wallet->fillOrderInfo($order);

        DB::beginTransaction();

        $product->save();

        $order->product_id = $product->id;

        $order->save();

        DB::commit();

        return $order;
    }

    public function getOrderCalculator(): OrderCalculator
    {
        return $this->orderCalculator;
    }

    public function getPaymentMethods(Client $client, ProductType $productType): array
    {
        return array_filter(
            $client->seller->getSupportedPaymentMethods(),
            fn (PaymentMethod $paymentMethod) => in_array($paymentMethod->getValue(), $productType->payment_methods, true)
        );
    }

    private function resolveDriver(Client $client, Location $location): ?Driver
    {
        /** @var Driver $driver */
        $driver = $client->source->drivers()
            ->where(
                fn (Builder $builder) => $builder
                    ->whereHas('locations', fn ($builder) => $builder->where('id', $location->id))
                    ->orWhereHas('locations.ancestors', fn ($builder) => $builder->where('id', $location->id))
                    ->orWhereHas('locations.descendants', fn ($builder) => $builder->where('id', $location->id))
            )
            ->whereNotNull('client_id')
            ->inRandomOrder()
            ->first();

        return $driver;
    }
}

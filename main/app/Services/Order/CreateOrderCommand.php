<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Client;
use App\Models\Location;
use App\Models\Order;
use App\Models\ProductType;
use App\Models\Shift;
use App\Services\Location\Exceptions\LocationIsNotFinalException;
use App\Services\Order\Exceptions\PaymentMethodNotFoundException;
use App\Services\Order\Exceptions\ProductForOrderNotFoundException;
use App\Services\Order\Exceptions\ShiftNotFoundException;
use App\Services\Order\Exceptions\ShouldSpecifyPaymentMethodException;
use App\Services\Order\VO\OrderStatus;
use App\Services\Product\Exceptions\LocationNotFoundException;
use App\Services\Product\Exceptions\ProductTypeNotFoundException;
use App\Services\Product\VO\ProductStatus;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Class CreateOrderCommand.
 */
final class CreateOrderCommand
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

    public function execute(Client $client, CreateOrderDto $dto): Order
    {
        $productType = ProductType::whereSellerId($client->seller_id)->whereNumber($dto->getProductTypeNumber())->first();

        if (!$productType) {
            throw new ProductTypeNotFoundException("Product {$dto->getProductTypeNumber()} not found");
        }

        $location = Location::whereSellerId($client->seller_id)->whereNumber($dto->getLocationNumber())->first();

        if (!$location) {
            throw new LocationNotFoundException("Location {$dto->getLocationNumber()} not found");
        }

        if ($location->children()->count() > 0) {
            throw new LocationIsNotFinalException(
                "Location {$dto->getLocationNumber()} is not final",
                $productType,
                $location
            );
        }

        $product = $this->productResolver->resolve($productType, $location);

        if (!$product) {
            throw new ProductForOrderNotFoundException(
                "Product {$dto->getProductTypeNumber()}/{$dto->getLocationNumber()} not found",
                $productType,
                $location
            );
        }

        $shift = Shift::whereSellerId($client->seller_id)->current()->first();

        if (!$shift) {
            throw new ShiftNotFoundException("There isn't current shift");
        }

        if (!$dto->getPaymentMethod()) {
            throw new ShouldSpecifyPaymentMethodException(
                '',
                $productType,
                $location,
                array_filter(
                    $client->seller->getSupportedPaymentMethods(),
                    fn (PaymentMethod $paymentMethod) => in_array($paymentMethod->getValue(), $productType->payment_methods ?? [], true)
                )
            );
        }

        try {
            $paymentMethod = new PaymentMethod((int) $dto->getPaymentMethod());
        } catch (InvalidArgumentException $e) {
            throw new PaymentMethodNotFoundException();
        }

        $walletType = WalletType::createByPaymentMethod($paymentMethod);

        $wallet = $this->walletResolver->resolve($client->seller, $paymentMethod);

        $order = new Order();

        $order->seller_id = $client->seller_id;
        $order->client_id = $client->id;
        $order->product_id = $product->id;
        $order->shift_id = $shift->id;
        $order->wallet_id = $wallet->id;
        $order->wallet_type = $walletType->getModelMorphClass();
        $order->payment_method = $paymentMethod;
        $order->source_id = $dto->getSource()->getId();
        $order->source_type = $dto->getSource()->getType()->getValue();
        $order->status = new OrderStatus(OrderStatus::STATUS_AWAITING_PAYMENT);

        [$order->commission, $discountItem, $order->discount_amount, $totalPrice] = $this->orderCalculator->calc($product, $client);

        $order->discount_id = $discountItem->getDiscountId();

        $order->price = $product->productType->price;
        $order->total_price = $totalPrice;
        $order->unpaid_amount = $totalPrice;

        $order->name = $product->productType->name;
        $order->packing = $product->productType->packing;
        $order->unit = $product->productType->unit->getReadableValue();

        $wallet->fillOrderInfo($order);

        DB::beginTransaction();

        $order->save();

        $product->status = new ProductStatus(ProductStatus::STATUS_BOOKED);
        $product->booked_at = now();

        $product->save();

        DB::commit();

        return $order;
    }
}

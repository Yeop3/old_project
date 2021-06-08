<?php

declare(strict_types=1);

namespace App\Services\ProductType;

use App\Models\Location;
use App\Models\ProductType;
use App\Models\Seller;
use App\Services\ProductType\VO\DeliveryType;
use App\Services\Wallet\VO\PaymentMethod;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateProductTypeCommand.
 */
final class UpdateProductTypeCommand
{
    private Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function execute(int $productTypeNumber, Seller $seller, ProductTypeDto $dto): ProductType
    {
        $this->checker->checkDuplicate($seller, $dto, $productTypeNumber);

        $productType = ProductType::whereSellerId($seller->id)->whereNumber($productTypeNumber)->firstOrFail();

        $productType->name = $dto->getName();
        $productType->price = $dto->getPrice();
        $productType->commission = $dto->getCommission();
        $productType->packing = $dto->getPacking();
        $productType->unit = $dto->getUnit();
        $productType->active = $dto->isActive();
        $productType->payment_methods = array_map(
            fn (PaymentMethod $paymentMethod) => $paymentMethod->getValue(),
            $dto->getPaymentMethods()
        );

        $productType->delivery_type = !$productType->packing
            ? DeliveryType::taxi()
            : DeliveryType::treasure();

        $locationsIds = $dto->getLocationNumbers()
            ? Location::whereSellerId($seller->id)
                ->whereIn('number', $dto->getLocationNumbers())
                ->pluck('id')
            : [];

        DB::beginTransaction();

        $productType->save();

        $productType->locations()->sync($locationsIds);

        DB::commit();

        return $productType;
    }
}

<?php

namespace App\Services\Product\ActionsSelectProduct;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Product\VO\ProductStatus;

/**
 * Class ActionsSelectProductCommand.
 */
final class ActionsSelectProductCommand
{
    public function execute(ActionsSelectProductDto $dto): array
    {
        /* @var User $user */
        $user = auth()->user();
        $errors = [
            'errors'=> [],
        ];
        $products = Product::whereSellerId($user->seller_id)->whereIn('number', $dto->getNumbers())
            ->get();
        switch ($dto->getType()) {
            case 'delete_select':
                $products->each(function (Product $product) use (&$errors) {
                    if (!($product->status->getValue() === ProductStatus::STATUS_BOOKED)) {
                        $product->delete();
                    }

                    if ($product->status->getValue() === ProductStatus::STATUS_BOOKED) {
                        $errors['errors'][] = 'Клад '.$product->number.' не может быть удален, так как он забронирован';
                    }

                    if (Order::whereSellerId($product->seller_id)->whereProductId($product->id)->exists()) {
                        $errors['errors'][] = 'Клад '.$product->number.' не может быть удален, так как он привязан к заказу';
                    }
                });
                break;
            case 'change_sell':
                $products->each(function (Product $product) use (&$errors) {
                    if ($product->status->isEditable()) {
                        $product->status = new ProductStatus(ProductStatus::STATUS_SELL);
                        $product->save();
                    }

                    if ($product->status->getValue() === ProductStatus::STATUS_BOOKED) {
                        $errors['errors'][] = 'Клад '.$product->number.' не может изменить статус, так как он забронирован';
                    }
                    if ($product->status->getValue() === ProductStatus::STATUS_SOLD) {
                        $errors['errors'][] = 'Клад '.$product->number.' не может изменить статус, так как он продан';
                    }
//                    if ($product->status->getValue() === ProductStatus::STATUS_RELOCATION){
//                        array_push($errors['errors'], 'Клад '.$product->number.' не может изменить статус, так как он находится на перекладе');
//                    }
                });
                break;
            case 'change_not_active':
                $products->each(function (Product $product) use (&$errors) {
                    if ($product->status->isEditable()) {
                        $product->status = new ProductStatus(ProductStatus::STATUS_NOT_ACTIVE);
                        $product->save();
                    }

                    if ($product->status->getValue() === ProductStatus::STATUS_BOOKED) {
                        array_push($errors['errors'], 'Клад '.$product->number.' не может изменить статус, так как он забронирован');
                    }
                    if ($product->status->getValue() === ProductStatus::STATUS_SOLD) {
                        array_push($errors['errors'], 'Клад '.$product->number.' не может изменить статус, так как он продан');
                    }
//                    if ($product->status->getValue() === ProductStatus::STATUS_RELOCATION) {
//                        array_push($errors['errors'], 'Клад '.$product->number.' не может изменить статус, так как он находится на перекладе');
//                    }
                });
                break;
        }

        return $errors;
    }
}

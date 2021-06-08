<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use App\Services\Product\ProductDto;
use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateProductRequest.
 */
class CreateProductRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        $commissionType = $this->get('commission_type');

        //        if (parseBool($this->get('check_duplicates'))) {
//            $rules['address'][] = ProductRules::addressArray($user->seller_id);
//        }

        return [
            'driver_id'        => ProductRules::driverId($user->seller_id),
            'bots_numbers'     => ProductRules::botsNumbers(),
            'bots_numbers.*'   => ProductRules::botsNumbersItem($user->seller_id),
            'product_type_id'  => ProductRules::productTypeId($user->seller_id),
            'location_id'      => ProductRules::localtionId($user->seller_id),
            'commission_type'  => ProductRules::comissionType(),
            'status'           => ProductRules::status(),
            'address'          => ProductRules::address(),
            'images'           => ProductRules::images(),
            'images.*'         => ProductRules::imagesItem(),
            'video'            => ProductRules::video(),
            'coordinates'      => ProductRules::coordinates($user->seller_id),
            'commission_value' => ProductRules::comissionValue($commissionType),
        ];
    }

    public function getDto(): ProductDto
    {
        return new ProductDto(
            parseIntFromInput($this->get('driver_id')),
            parseIntFromInput($this->get('product_type_id')),
            parseIntFromInput($this->get('location_id')),
            new Commission(
                (int) $this->get('commission_value') ?: 0,
                new CommissionType((int) $this->get('commission_type'))
            ),
            $this->get('coordinates'),
            $this->file('images'),
            new ProductStatus(parseIntFromInput($this->get('status'))),
            $this->file('video'),
            $this->get('address'),
            $this->get('bots_numbers'),
        );
    }
}

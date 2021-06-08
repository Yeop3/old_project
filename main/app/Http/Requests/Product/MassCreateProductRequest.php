<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use App\Services\Product\ProductMassDto;
use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MassCreateProductRequest.
 */
class MassCreateProductRequest extends FormRequest
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

        $rules = [
            'driver_id'        => ['required', 'integer', Rule::exists('drivers', 'number')->where('seller_id', $user->seller_id)],
            'product_type_id'  => ['required', 'integer', Rule::exists('product_types', 'number')->where('seller_id', $user->seller_id)],
            'location_id'      => ['required', 'integer', Rule::exists('locations', 'number')->where('seller_id', $user->seller_id)],
            'commission_type'  => ['required', Rule::in(array_keys(CommissionType::TYPES))],
            'status'           => ['required', Rule::in(ProductStatus::EDITABLE_STATUSES)],
            'images'           => ['required', 'array', 'min:1'],
            'images.*'         => ['required', 'image', 'max:10000'],
            'addresses'        => ['nullable', 'array', 'min:0'],
            'addresses.*'      => ['required', 'string', 'max:255'],
            'coordinates'      => ['nullable', 'array', 'min:1'],
            'coordinates.*'    => ProductRules::coordinates(),
            'check_duplicates' => ['boolean'],
            'bots_numbers'     => ['array'],
            'bots_numbers.*'   => ['integer', Rule::exists('bots', 'number')->where('seller_id', $user->seller_id)],
        ];

//        if (parseBool($this->get('check_duplicates'))) {
//            $rules['addresses.*'][] = Rule::unique('products', 'address')->where('seller_id', $user->seller_id);
//        }

        if ($commissionType === CommissionType::TYPE_PERCENT) {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0', 'max:100'];
        } else {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function getDto(): ProductMassDto
    {
        return new ProductMassDto(
            parseIntFromInput($this->get('driver_id')),
            parseIntFromInput($this->get('product_type_id')),
            parseIntFromInput($this->get('location_id')),
            new Commission(
                (int) $this->get('commission_value') ?: 0,
                new CommissionType((int) $this->get('commission_type'))
            ),
            $this->get('coordinates'),
            $this->file('images'),
            $this->get('addresses'),
            new ProductStatus(parseIntFromInput($this->get('status'))),
            $this->get('bots_numbers')
        );
    }
}

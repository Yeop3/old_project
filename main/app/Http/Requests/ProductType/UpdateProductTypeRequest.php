<?php

namespace App\Http\Requests\ProductType;

use App\Models\User;
use App\Services\ProductType\ProductTypeDto;
use App\Services\Wallet\VO\PaymentMethod;
use App\VO\Commission;
use App\VO\CommissionType;
use App\VO\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Money\Currency;
use Money\Money;

/**
 * Class UpdateProductTypeRequest.
 */
class UpdateProductTypeRequest extends FormRequest
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

        $productTypeId = $this->route('product_type');

        $commissionType = $this->get('commission_type');

        $rules = [
            'name' => [
                'required',
                'max:255',
                Rule::unique('product_types')->ignore($productTypeId, 'number')->where('seller_id', $user->seller_id),
            ],
            'price'              => ['required', 'numeric', 'min: 1'],
            'commission_type'    => ['required', Rule::in(array_keys(CommissionType::TYPES))],
            'packing'            => ['nullable', 'integer', 'min:1'],
            'unit'               => ['nullable', 'integer', Rule::in(array_keys(Unit::UNITS))],
            'active'             => ['required', 'boolean'],
            'payment_methods'    => ['required', 'array'],
            'payment_methods.*'  => ['required', 'integer', Rule::in(array_keys(PaymentMethod::TYPES))],
            'location_numbers.*' => [
                'required',
                'integer',
                Rule::exists('locations', 'number')->where('seller_id', $user->seller_id),
            ],
        ];

        if ($commissionType === CommissionType::TYPE_PERCENT) {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0', 'max:100'];
        } else {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function getDto(): ProductTypeDto
    {
        return new ProductTypeDto(
            $this->get('name'),
            new Money((float) $this->get('price') * 100, new Currency('UAH')),
            new Commission(
                (int) $this->get('commission_value') ?: 0,
                new CommissionType((int) $this->get('commission_type'))
            ),
            $this->get('packing') ? (int) $this->get('packing') : null,
            $this->get('unit') ? new Unit((int) $this->get('unit')) : null,
            array_map(
                fn (string $paymentMethodValue) => new PaymentMethod((int) $paymentMethodValue),
                $this->get('payment_methods')
            ),
            $this->get('location_numbers'),
            parseBool($this->get('active'))
        );
    }
}

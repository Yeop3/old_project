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
 * Class CreateProductTypeRequest.
 */
class CreateProductTypeRequest extends FormRequest
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

        return [
            'name'               => ProductTypeRules::name($user->seller_id),
            'price'              => ProductTypeRules::price(),
            'commission_type'    => ProductTypeRules::comissionType(),
            'packing'            => ProductTypeRules::packing(),
            'unit'               => ProductTypeRules::unit(),
            'commission_value'   => ProductTypeRules::comissionValue($commissionType),
            'active'             => ['required', 'boolean'],
            'payment_methods'    => ['required', 'array'],
            'payment_methods.*'  => ['required', 'integer', Rule::in(array_keys(PaymentMethod::TYPES))],
            'location_numbers'   => ['nullable', 'array'],
            'location_numbers.*' => [
                'required',
                'integer',
                Rule::exists('locations', 'number')->where('seller_id', $user->seller_id),
            ],
        ];
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

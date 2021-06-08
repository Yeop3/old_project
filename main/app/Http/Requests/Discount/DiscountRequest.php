<?php

namespace App\Http\Requests\Discount;

use App\Models\User;
use App\Services\Discount\DiscountDto;
use App\Services\Discount\VO\DiscountDateRange;
use App\Services\Discount\VO\DiscountValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class DiscountRequest.
 */
class DiscountRequest extends FormRequest
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

        return [
            'name'               => ['required', 'max:255'],
            'discount_value'     => ['required', 'numeric', 'min:0', 'max:99.99'],
            'discount_priority'  => ['required', 'numeric', 'min:0', 'max:1000000'],
            'active'             => ['required', 'boolean'],
            'location_numbers'   => ['required', 'array', 'min:1'],
            'location_numbers.*' => [
                'required',
                'integer',
                Rule::exists('locations', 'number')->where('seller_id', $user->seller_id),
            ],
            'product_type_numbers'   => ['required', 'array', 'min:1'],
            'product_type_numbers.*' => [
                'required',
                'integer',
                Rule::exists('product_types', 'number')->where('seller_id', $user->seller_id),
            ],
            'date_start'                   => ['nullable', 'date'],
            'date_end'                     => ['nullable', 'date'],
            'client_min_paid_orders_count' => ['required', 'integer', 'min:0'],
            'client_min_income'            => ['required', 'integer', 'min:0'],
            'description'                  => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function getDto(): DiscountDto
    {
        return new DiscountDto(
            $this->get('name'),
            new DiscountValue(parseFloatFromInput($this->get('discount_value'))),
            parseIntFromInput($this->get('discount_priority')),
            parseBool($this->get('active')),
            collect($this->get('location_numbers'))
                ->map(fn (string $number) => (int) $number)
                ->toArray(),
            collect($this->get('product_type_numbers'))
                ->map(fn (string $number) => (int) $number)
                ->toArray(),
            new DiscountDateRange(
                carbonSafeParse($this->get('date_start')),
                carbonSafeParse($this->get('date_end')),
            ),
            parseIntFromInput($this->get('client_min_paid_orders_count')),
            parseIntFromInput($this->get('client_min_income')),
            $this->get('description'),
        );
    }
}

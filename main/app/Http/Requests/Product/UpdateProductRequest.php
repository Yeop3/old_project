<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use App\Services\Product\ProductDto;
use App\Services\Product\VO\ProductStatus;
use App\VO\Commission;
use App\VO\CommissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateProductRequest.
 */
class UpdateProductRequest extends FormRequest
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

        $productId = $this->route('product');

        $rules = [
            'driver_id'       => ['required', 'integer', Rule::exists('drivers', 'number')->where('seller_id', $user->seller_id)],
            'product_type_id' => ['required', 'integer', Rule::exists('product_types', 'number')->where('seller_id', $user->seller_id)],
            'location_id'     => ['required', 'integer', Rule::exists('locations', 'number')->where('seller_id', $user->seller_id)],
            'commission_type' => ['required', Rule::in(array_keys(CommissionType::TYPES))],
            'status'          => ['required', Rule::in(array_keys(ProductStatus::STATUSES))],
            'address'         => [
                'nullable',
                'string',
                'max:255',
                //                Rule::unique('products', 'address')->ignore($productId, 'id')->where('seller_id', $user->seller_id)
            ],
            'images'      => ['nullable', 'array', 'min:0'],
            'images.*'    => ['required', 'image', 'max:10000'],
            'video'       => ['nullable', 'file', 'mimes:avi,mpeg,quicktime,mp4', 'max:50000'],
            'coordinates' => ProductRules::coordinates($user->seller_id, $productId),
        ];

        if ($commissionType === CommissionType::TYPE_PERCENT) {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0', 'max:100'];
        } else {
            $rules['commission_value'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
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
        );
    }
}

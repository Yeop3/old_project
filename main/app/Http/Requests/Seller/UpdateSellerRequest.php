<?php

namespace App\Http\Requests\Seller;

use App\Services\Seller\Update\UpdateSellerDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSellerRequest.
 */
class UpdateSellerRequest extends FormRequest
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
        $sellerId = $this->route('seller');

        $rules = [
            'name'   => ['required', 'max:255'],
            'domain' => ['required', 'max:255', Rule::unique('sellers')->ignore($sellerId)],
        ];

        if ($this->get('password')) {
            $rules = array_merge($rules, [
                'password' => ['required', 'min:6', 'max:255', 'confirmed'],
            ]);
        }

        return $rules;
    }

    public function getDto(): UpdateSellerDto
    {
        return new UpdateSellerDto(
            $this->get('name'),
            $this->get('domain'),
            $this->get('password'),
        );
    }
}

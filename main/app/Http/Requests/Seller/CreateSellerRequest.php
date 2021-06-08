<?php

namespace App\Http\Requests\Seller;

use App\Services\Seller\Create\CreateSellerDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateSellerRequest.
 */
class CreateSellerRequest extends FormRequest
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
        return [
            'name'     => ['required', 'max:255'],
            'domain'   => ['required', 'max:255', Rule::unique('sellers')],
            'password' => ['required', 'min:6', 'max:255', 'confirmed'],
        ];
    }

    public function getDto(): CreateSellerDto
    {
        return new CreateSellerDto(
            $this->get('name'),
            $this->get('domain'),
            $this->get('password'),
        );
    }
}

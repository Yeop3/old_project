<?php

namespace App\Http\Requests\Driver;

use App\Models\User;
use App\Services\Driver\DriverDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateDriverRequest.
 */
class CreateDriverRequest extends FormRequest
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
            'name'               => [
                'required',
                'max:255',
                Rule::unique('drivers')->where('seller_id', $user->seller_id),
            ],
            'client_number'      => [
                'required',
                'integer',
                Rule::exists('clients', 'number')->where('seller_id', $user->seller_id),
            ],
            'location_numbers'   => ['nullable', 'array'],
            'location_numbers.*' => [
                'required',
                'integer',
                Rule::exists('locations', 'number')->where('seller_id', $user->seller_id),
            ],
        ];
    }

    public function getDto(): DriverDto
    {
        return new DriverDto(
            $this->get('name'),
            parseIntFromInput($this->get('client_number')),
            $this->get('location_numbers')
        );
    }
}

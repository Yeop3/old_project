<?php

namespace App\Http\Requests\Operators;

use App\Models\User;
use App\Services\Operator\OperatorDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateOperatorsRequest.
 */
class UpdateOperatorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        $operatorID = $this->route('operator');

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('operators')->ignore($operatorID, 'number')->where('seller_id', $user->seller_id),
            ],
            'client_number' => [
                'required',
                'integer',
                Rule::exists('clients', 'number')->where('seller_id', $user->seller_id),
            ],
//            'password' => [
//                'nullable',
//                'min:6',
//                'string'
//            ],
        ];
    }

    public function getDto(): OperatorDto
    {
        return new OperatorDto(
            $this->get('name'),
            parseIntFromInput($this->get('client_number')),
            $this->get('email'),
            $this->get('password')
        );
    }
}

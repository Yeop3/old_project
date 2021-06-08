<?php

namespace App\Http\Requests\Client;

use App\Models\User;
use App\Services\Client\MultiBan\MultiBanClientDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MultiBanRequest.
 */
class MultiBanRequest extends FormRequest
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
        /* @var User $user */
        $user = auth()->user();

        return [
            'numbers'   => ['required', 'array', 'min:1'],
            'numbers.*' => [
                'integer',
                Rule::exists('clients', 'number')
                    ->where('seller_id', $user->seller_id),
            ],
            'period'    => ['required', 'integer'],
        ];
    }

    public function getDto(): MultiBanClientDto
    {
        return new MultiBanClientDto(
            $this->get('numbers'),
            $this->get('period')
        );
    }
}

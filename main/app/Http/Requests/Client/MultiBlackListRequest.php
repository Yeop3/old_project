<?php

namespace App\Http\Requests\Client;

use App\Models\User;
use App\Services\Client\MultiBlackList\MultiBlackListClientDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MultiBlackListRequest.
 */
class MultiBlackListRequest extends FormRequest
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
        ];
    }

    public function getDto(): MultiBlackListClientDto
    {
        return new MultiBlackListClientDto(
            $this->get('numbers')
        );
    }
}

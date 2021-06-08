<?php

namespace App\Http\Requests\Dispatch;

use App\Models\User;
use App\Services\Dispatch\Create\DispatchCreateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateRequest.
 */
class CreateRequest extends FormRequest
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
        /* @var  User $user */
        $user = auth()->user();

        return [
            'bots_numbers'   => ['array', 'min:1'],
            'bots_numbers.*' => ['integer', Rule::exists('bots', 'number')->where('seller_id', $user->seller_id)],
            'messages'       => ['required', 'string'],
        ];
    }

    public function getDto(): DispatchCreateDto
    {
        return new DispatchCreateDto(
            $this->get('bots_numbers'),
            $this->get('messages')
        );
    }
}

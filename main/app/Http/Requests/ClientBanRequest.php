<?php

namespace App\Http\Requests;

use App\Services\Client\Ban\BanClientDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClientBanRequest.
 */
class ClientBanRequest extends FormRequest
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
        return [
            'days' => ['integer', 'required'],
        ];
    }

    public function getDto(): BanClientDto
    {
        return new BanClientDto(
            (int) $this->get('days')
        );
    }
}

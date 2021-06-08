<?php

namespace App\Http\Requests\Client;

use App\Models\User;
use App\Services\Client\Update\UpdateClientDto;
use App\Services\Discount\VO\DiscountValue;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateClientRequest.
 */
class UpdateClientRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'note'              => ['nullable', 'string', 'max:255'],
            'discount_value'    => ['required', 'numeric', 'min:0', 'max:99.99'],
            'discount_priority' => ['required', 'numeric', 'min:0', 'max:1000000'],
        ];
    }

    public function getDto(): UpdateClientDto
    {
        return new UpdateClientDto(
            new DiscountValue(parseFloatFromInput($this->get('discount_value'))),
            parseIntFromInput($this->get('discount_priority')),
            $this->get('note'),
        );
    }
}

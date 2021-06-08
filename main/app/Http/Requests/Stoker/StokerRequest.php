<?php

namespace App\Http\Requests\Stoker;

use App\Services\Stoker\StokerDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StokerRequest.
 */
class StokerRequest extends FormRequest
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
        return [
            'client_number'       => ['required', 'nullable', 'min:1'],
            'location_number'     => ['required', 'integer', 'min:1'],
            'product_type_number' => ['required', 'integer', 'min:1'],
            'source_number'       => ['required', 'integer', 'min:1'],
        ];
    }

    public function getDto(): StokerDto
    {
        return new StokerDto(
            $this->get('source_number'),
            $this->get('location_number'),
            $this->get('client_number'),
            $this->get('product_type_number')
        );
    }
}

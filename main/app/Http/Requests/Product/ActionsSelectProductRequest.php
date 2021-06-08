<?php

namespace App\Http\Requests\Product;

use App\Services\Product\ActionsSelectProduct\ActionsSelectProductDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ActionsSelectProductRequest.
 */
class ActionsSelectProductRequest extends FormRequest
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
            'numbers'   => 'required|array|min:1',
            'numbers.*' => 'required|integer',
            'type'      => 'required|string',
        ];
    }

    /**
     * @return ActionsSelectProductDto
     */
    public function getDto(): ActionsSelectProductDto
    {
        return new ActionsSelectProductDto(
            $this->get('numbers'),
            $this->get('type'),
        );
    }
}

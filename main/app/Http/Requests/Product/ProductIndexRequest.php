<?php

namespace App\Http\Requests\Product;

use App\Services\Product\Index\IndexProductDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProductIndexRequest.
 */
class ProductIndexRequest extends FormRequest
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
            'location'      => ['nullable', 'integer'],
            'driver'        => ['nullable', 'integer'],
            'product_type'  => ['nullable', 'integer'],
            'number'        => ['nullable', 'integer'],
            'status'        => ['nullable', 'integer'],
            'address'       => ['nullable', 'string'],
            'sortField'     => ['nullable', 'string'],
            'sortDirection' => ['nullable', 'string'],
            'coordinates'   => ['nullable', 'string'],
            'comission'     => ['nullable', 'integer'],
            'created_at'    => ['nullable', 'string'],
        ];
    }

    public function getDto(): IndexProductDto
    {
        return new IndexProductDto(
            parseIntFromInput($this->get('location')),
            parseIntFromInput($this->get('driver')),
            parseIntFromInput($this->get('product_type')),
            $this->get('address'),
            parseIntFromInput($this->get('number')),
            parseIntFromInput($this->get('status')),
            $this->get('sortField'),
            $this->get('sortDirection'),
            $this->get('coordinates'),
            $this->get('comission'),
            $this->get('created_at'),
        );
    }
}

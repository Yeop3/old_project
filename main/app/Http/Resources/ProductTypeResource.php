<?php

namespace App\Http\Resources;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductTypeResource.
 */
class ProductTypeResource extends JsonResource
{
    /**
     * @var ProductType
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return array_merge($this->resource->toArray(), [
            'price' => formatMoney($this->resource->price),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductType\CreateProductTypeRequest;
use App\Http\Requests\ProductType\UpdateProductTypeRequest;
use App\Http\Resources\ProductTypeResource;
use App\Models\ProductType;
use App\Models\User;
use App\Services\ProductType\CreateProductTypeCommand;
use App\Services\ProductType\DeleteProductTypeCommand;
use App\Services\ProductType\UpdateProductTypeCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

/**
 * Class ProductTypeController.
 */
class ProductTypeController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();
        $product = ProductType::whereSellerId($user->seller_id);
        if ($name = \request('name')) {
            $product->where('name', 'like', "%{$name}%");
        }
        if (\request('sortDirection') === 'asc') {
            $column = \request('sortField');
            $product->orderBy($column);
        }
        if (\request('sortDirection') === 'desc') {
            $column = \request('sortField');
            $product->orderByDesc($column);
        }
        if ($number = \request('number')) {
            $product->where('number', 'like', "%{$number}%");
        }
        if ($comission = \request('comission')) {
            $product->where('commission_value', $comission);
        }
        if ($price = \request('price')) {
            $correctPrice = $price * 100;
            $product->where('price', 'like', "%{$correctPrice}%");
        }

        return ProductTypeResource::collection(
            $product->paginate(20)->appends('location_numbers')
        );
    }

    /**
     * @return ProductType[]|Builder[]|Collection
     */
    public function forSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return ProductType::whereSellerId($user->seller_id)->get(['name as text', 'number as value', 'delivery_type']);
    }

    /**
     * @param $id
     *
     * @return ProductTypeResource
     */
    public function show($id): ProductTypeResource
    {
        /** @var User $user */
        $user = auth()->user();

        return new ProductTypeResource(
            ProductType::whereSellerId($user->seller_id)->whereNumber($id)->firstOrFail()->append('location_numbers')
        );
    }

    /**
     * @param CreateProductTypeRequest $request
     * @param CreateProductTypeCommand $command
     *
     * @return ProductTypeResource
     */
    public function store(CreateProductTypeRequest $request, CreateProductTypeCommand $command): ProductTypeResource
    {
        /** @var User $user */
        $user = auth()->user();

        return new ProductTypeResource(
            $command->execute($user->seller, $request->getDto())
        );
    }

    /**
     * @param UpdateProductTypeRequest $request
     * @param UpdateProductTypeCommand $command
     * @param $id
     *
     * @return ProductTypeResource
     */
    public function update(UpdateProductTypeRequest $request, UpdateProductTypeCommand $command, $id): ProductTypeResource
    {
        /** @var User $user */
        $user = auth()->user();

        return new ProductTypeResource(
            $command->execute((int) $id, $user->seller, $request->getDto())
        );
    }

    /**
     * @param DeleteProductTypeCommand $command
     * @param $id
     */
    public function destroy(DeleteProductTypeCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param Request                  $request
     * @param DeleteProductTypeCommand $command
     *
     * @return int[]
     */
    public function destroyMass(Request $request, DeleteProductTypeCommand $command): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->executeMass(
            collect($request->get('ids'))
                ->map(fn ($id) => (int) $id)
                ->toArray(),
            $user->seller
        );
    }
}

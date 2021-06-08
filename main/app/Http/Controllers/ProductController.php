<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ActionsSelectProductRequest;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\MassCreateProductRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\User;
use App\Services\Product\ActionsSelectProduct\ActionsSelectProductCommand;
use App\Services\Product\Checker;
use App\Services\Product\Create\CreateProductCommand;
use App\Services\Product\Create\MassCreateResult;
use App\Services\Product\DeleteProductCommand;
use App\Services\Product\Index\IndexProductCommand;
use App\Services\Product\Update\UpdateProductCommand;
use App\Services\Product\VO\ProductStatus;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

/**
 * Class ProductController.
 */
class ProductController extends Controller
{
    /**
     * @param ProductIndexRequest $request
     * @param IndexProductCommand $command
     *
     * @return LengthAwarePaginator
     */
    public function index(ProductIndexRequest $request, IndexProductCommand $command): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $command->execute($request->getDto());
    }

    public function getStatus(): array
    {
        return ProductStatus::STATUSES;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();
        $product = Product::with(['driver',
            'productType',
            'location',
            'location.ancestors',
            'photos',
        ])
            ->whereSellerId($user->seller_id)
            ->whereNumber($id)
            ->firstOrFail();

        if ($product->image_src) {
            $product->photos->prepend([
                'src'    => $product->image_src,
                'url'    => $product->image_url,
                'number' => 1,
            ]);
        }

        $product->location->append(['name_chain']);

        return $product;
    }

    /**
     * @param CreateProductRequest $request
     * @param CreateProductCommand $command
     *
     * @throws Throwable
     *
     * @return Product
     */
    public function store(CreateProductRequest $request, CreateProductCommand $command): Product
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param MassCreateProductRequest $request
     * @param CreateProductCommand     $command
     *
     * @return MassCreateResult
     */
    public function storeMass(MassCreateProductRequest $request, CreateProductCommand $command): \App\Services\Product\Create\MassCreateResult
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->executeMass($user->seller, $request->getDto());
    }

    /**
     * @param UpdateProductRequest $request
     * @param UpdateProductCommand $command
     * @param $id
     *
     * @return Product
     */
    public function update(UpdateProductRequest $request, UpdateProductCommand $command, $id): Product
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteProductCommand $command
     * @param $id
     */
    public function destroy(DeleteProductCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param $number
     *
     * @throws Exception
     */
    public function destroyPhoto($number): void
    {
        /** @var User $user */
        $user = auth()->user();

        $productPhoto = ProductPhoto::whereSellerId($user->seller_id)->whereNumber($number)->firstOrFail();

        $path = storage_path('app/public'.$productPhoto->src);

        $productPhoto->delete();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @param Request              $request
     * @param DeleteProductCommand $command
     *
     * @return int[]
     */
    public function destroyMass(Request $request, DeleteProductCommand $command): array
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

    /**
     * @param ActionsSelectProductRequest $request
     * @param ActionsSelectProductCommand $command
     *
     * @return array[]
     */
    public function actionsSelect(ActionsSelectProductRequest $request, ActionsSelectProductCommand $command): array
    {
        return $command->execute($request->getDto());
    }

    /**
     * @param Request $request
     * @param Checker $checker
     */
    public function changeStatus(Request $request, Checker $checker): void
    {
        /** @var User $user */
        $user = auth()->user();

        $product = Product::whereSellerId($user->seller_id)->whereNumber($request->get('number'))->firstOrFail();

        $status = new ProductStatus((int) $request->get('status'));
        if ($product->status->isEditable()) {
            $checker->checkStatus($status);

            $product->status = $status;
        }

        $product->save();
    }
}

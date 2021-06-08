<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discount\DiscountRequest;
use App\Models\Discount;
use App\Models\Location;
use App\Models\User;
use App\Services\Discount\CreateDiscountCommand;
use App\Services\Discount\DeleteDiscountCommand;
use App\Services\Discount\UpdateDiscountCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class DiscountController.
 */
class DiscountController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $discountPagination = Discount::whereSellerId($user->seller_id)
            ->with([
                'productTypes.products',
                'locations.ancestors',
            ])
            ->numberFilter($request->get('number'))
            ->nameFilter($request->get('name'))
            ->discountFilter($request->get('discount_value'))
            ->statusFilter($request->get('status'))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'));

        $discountPagination = $discountPagination->paginate(20);
        $discountPagination->getCollection()->each(function (Discount $discount) {
            $discount->locations->append('name_chain');

            return $discount;
        });

        return $discountPagination;
    }

    /**
     * @param $id
     *
     * @return Discount|Builder|Model
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();
        $discount = Discount::whereSellerId($user->seller_id)->whereNumber($id)
            ->with([
                'productTypes.products',
                'locations.ancestors',
            ])
            ->firstOrFail();
        $discount->locations->each(fn (Location $location) => $location->append('name_chain'));

        return $discount;
    }

    /**
     * @param DiscountRequest       $request
     * @param CreateDiscountCommand $command
     *
     * @return Discount
     */
    public function store(DiscountRequest $request, CreateDiscountCommand $command): Discount
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param DiscountRequest       $request
     * @param UpdateDiscountCommand $command
     * @param $id
     *
     * @return Discount
     */
    public function update(DiscountRequest $request, UpdateDiscountCommand $command, $id): Discount
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteDiscountCommand $command
     * @param $id
     */
    public function destroy(DeleteDiscountCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\CreateLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\Bot;
use App\Models\Location;
use App\Models\User;
use App\Services\Location\CreateLocationCommand;
use App\Services\Location\DeleteLocationCommand;
use App\Services\Location\UpdateLocationCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;

/**
 * Class LocationController.
 */
class LocationController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var LengthAwarePaginator $paginate */
        $paginate = Location::whereSellerId($user->seller_id)
            ->whereIsRoot()
            ->with(['descendants', 'descendants.products', 'products', 'drivers', 'descendants.drivers'])
            ->paginate(20);

        $paginate->setCollection(
            $paginate->getCollection()->map(function (Location $location) {
                $tree = $location->descendants->toTree();
                $location = $location->toArray();
                unset($location['descendants']);
                $location['children'] = $tree;

                return $location;
            })
        );

        return $paginate;
    }

//    public function getSelect()
//    {
//        /** @var User $user */
//        $user = auth()->user();
//        return Bot::standardTelegram()
//            ->when($clientNumber = request('client_number'), fn(Builder $q) => $q
//                ->whereHas('clients', fn(Builder $q) => $q
//                    ->where('number', $clientNumber)
//                )
//            )
//            ->whereSellerId($user->seller_id)->get(['username as text', 'number as value']);
//    }

    /**
     * @return mixed
     */
    public function forSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return Location::whereSellerId($user->seller_id)
            ->with('ancestors')
            ->withDepth()

            ->get()
            ->map(fn (Location $location) => [
                'value' => $location->number,
                'text'  => $location->name_chain,
                'depth' => $location->depth,
            ]);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return array
     */
    public function show(Request $request, $id): array
    {
        /** @var User $user */
        $user = $request->user();

        $location = Location::whereSellerId($user->seller_id)
            ->whereNumber($id)->with('parent', 'drivers')
            ->firstOrFail();
        $location->append('driver_numbers');
        $parent = $location->parent;

        $location = $location->toArray();
        $location['parent'] = optional($parent)->toArray();

        return $location;
    }

    /**
     * @param CreateLocationRequest $request
     * @param CreateLocationCommand $command
     *
     * @return Location
     */
    public function store(CreateLocationRequest $request, CreateLocationCommand $command): Location
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateLocationRequest $request
     * @param UpdateLocationCommand $command
     * @param $id
     *
     * @return Location
     */
    public function update(UpdateLocationRequest $request, UpdateLocationCommand $command, $id): Location
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute((int) ($id), $user->seller, $request->getDto());
    }

    /**
     * @param DeleteLocationCommand $command
     * @param $id
     */
    public function destroy(DeleteLocationCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param Request               $request
     * @param DeleteLocationCommand $command
     *
     * @return int[]
     */
    public function destroyMass(Request $request, DeleteLocationCommand $command): array
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

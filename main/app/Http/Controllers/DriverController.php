<?php

namespace App\Http\Controllers;

use App\Http\Requests\Driver\CreateDriverRequest;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\User;
use App\Services\Driver\CreateDriverCommand;
use App\Services\Driver\DeleteDriverCommand;
use App\Services\Driver\UpdateDriverCommand;
use App\Services\Driver\VO\PermissionTypes;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tightenco\Collect\Support\Collection;

/**
 * Class DriverController.
 */
class DriverController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        $drivers = Driver::whereSellerId($user->seller_id)
            ->with('client')
            ->numberFilter($request->get('number'))
            ->nameFilter($request->get('name'))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'));

        return $drivers->paginate(20);
    }

    /**
     * @return Driver[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function forSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return Driver::whereSellerId($user->seller_id)->get(['name as text', 'number as value']);
    }

    /**
     * @param $id
     *
     * @return Driver|Builder|Model
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();

        return Driver::whereSellerId($user->seller_id)
            ->whereNumber($id)
            ->with(['client', 'locations'])
            ->firstOrFail();
    }

    public function store(CreateDriverRequest $request, CreateDriverCommand $command): Driver
    {
        /** @var User $user */
        $user = $request->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateDriverRequest $request
     * @param UpdateDriverCommand $command
     * @param $id
     *
     * @return Driver
     */
    public function update(UpdateDriverRequest $request, UpdateDriverCommand $command, $id): Driver
    {
        /** @var User $user */
        $user = $request->user();

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteDriverCommand $command
     * @param $id
     */
    public function destroy(DeleteDriverCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    public function destroyMass(Request $request, DeleteDriverCommand $command): array
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
     * @return \Illuminate\Support\Collection|Collection
     */
    public function getPermissions()
    {
        return collect(PermissionTypes::TYPES)
            ->map(fn (string $label, int $key) => ['value' => $key, 'text' => $label])
            ->values();
    }
}

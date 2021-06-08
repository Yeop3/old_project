<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stoker\StokerRequest;
use App\Models\Stoker;
use App\Models\User;
use App\Services\Stoker\Create\CreateStokerCommand;
use App\Services\Stoker\Update\UpdateStokerCommand;

/**
 * Class StokerController.
 */
class StokerController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $stokers = Stoker::whereSellerId($user->seller_id)
            ->with(['location', 'productType', 'client', 'source', 'location.ancestors'])
            ->paginate(20);

        $stokers->getCollection()->each(function (Stoker $stoker) {
            $stoker->location->append('name_chain');

            return $stoker;
        });

        return $stokers;
    }

    /**
     * @param StokerRequest       $request
     * @param CreateStokerCommand $command
     *
     * @return Stoker
     */
    public function store(StokerRequest $request, CreateStokerCommand $command): Stoker
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller_id, $request->getDto());
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

        $stoker = Stoker::whereSellerId($user->seller_id)
            ->where('number', $id)
            ->with(['location', 'productType', 'client', 'source', 'location.ancestors'])
            ->firstOrFail();

        $stoker->location->append(['name_chain']);

        return $stoker;
    }

    /**
     * @param StokerRequest       $request
     * @param UpdateStokerCommand $command
     * @param $id
     *
     * @return Stoker
     */
    public function update(StokerRequest $request, UpdateStokerCommand $command, $id): Stoker
    {
        /** @var User $user */
        $user = auth()->user();

        $stoker = Stoker::whereSellerId($user->seller_id)->where('number', $id)->firstOrFail();

        return $command->execute($user->seller_id, $stoker, $request->getDto());
    }

    /**
     * @param $id
     */
    public function destroy($id): void
    {
        /** @var User $user */
        $user = auth()->user();

        Stoker::whereSellerId($user->seller_id)->where('number', $id)->firstOrFail()->delete();
    }
}

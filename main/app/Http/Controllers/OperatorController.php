<?php

namespace App\Http\Controllers;

use App\Http\Requests\Operators\CreateOperatorsRequest;
use App\Http\Requests\Operators\UpdateOperatorsRequest;
use App\Models\Operator;
use App\Models\Seller;
use App\Models\User;
use App\Services\Operator\CreateOperatorCommand;
use App\Services\Operator\DeleteOperatorCommand;
use App\Services\Operator\UpdateOperatorCommand;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class OperatorController.
 */
class OperatorController extends Controller
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Operator|Builder $operator */
        $operator = Operator::whereSellerId($user->seller_id)->with('client');
        if ($name = request('name')) {
            $operator->where('name', 'like', "%$name%");
        }

        return $operator->paginate(20);
    }

    /**
     * @return Operator[]|Builder[]|Collection
     */
    public function forSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return Operator::whereSellerId($user->seller_id)
            ->get(['number as value', 'name as text'])
            ->prepend(['text' => 'Все', 'value' => null]);
    }

    /**
     * @param $id
     *
     * @return Operator|Builder|Model
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();

        return Operator::whereSellerId($user->seller_id)->with('client')->whereNumber($id)->firstOrFail();
    }

    /**
     * @param CreateOperatorsRequest $request
     * @param CreateOperatorCommand  $command
     *
     * @return Operator|void
     */
    public function store(CreateOperatorsRequest $request, CreateOperatorCommand $command): Operator
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateOperatorsRequest $request
     * @param UpdateOperatorCommand  $command
     * @param $id
     *
     * @return Operator|void
     */
    public function update(UpdateOperatorsRequest $request, UpdateOperatorCommand $command, $id): Operator
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteOperatorCommand $command
     * @param $id
     *
     * @throws Exception
     */
    public function destroy(DeleteOperatorCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @return Operator[]|Builder[]|Collection
     */
    public function getListForBots()
    {
        /** @var User $user */
        $user = auth()->user();

        return Operator::whereSellerId($user->seller_id)
            ->with('client')
            ->get(['number as value', 'name as text', 'client_id']);
    }

    /**
     * @param Request $request
     *
     * @return Operator[]|Builder[]|Collection
     */
    public function getList(Request $request)
    {
        $host = $request->getHost();
        $seller = Seller::whereDomain($host)->firstOrFail();

        return Operator::whereSellerId($seller->id)
            ->get(['number as value', 'name as text']);
    }
}

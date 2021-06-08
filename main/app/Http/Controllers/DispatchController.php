<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dispatch\CreateRequest;
use App\Models\Dispatch;
use App\Models\User;
use App\Services\Dispatch\Create\DispatchCreateCommand;
use App\Services\Dispatch\ProductExist\DispatchProductExistCommand;
use danog\MadelineProto\auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DispatchController.
 */
class DispatchController extends Controller
{
    /**
     * @param DispatchProductExistCommand $command
     * @param $bot
     *
     * @return JsonResponse
     */
    public function getProductExistText(DispatchProductExistCommand $command, $bot): JsonResponse
    {
        return response()->json(['messages' => $command->execute((int) $bot)]);
    }

    /**
     * @param CreateRequest         $request
     * @param DispatchCreateCommand $command
     */
    public function store(CreateRequest $request, DispatchCreateCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();
        $command->execute($request->getDto(), $user->seller);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function index(Request $request): LengthAwarePaginator
    {
        /* @var User $user */
        $user = auth()->user();

        $dispatch = Dispatch::whereSellerId($user->seller_id)
            ->numberFilter($request->get('number'))
            ->botFilter($request->get('bot_id'))
            ->messageFilter($request->get('messages'))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'))
            ->with(['bot'])
            ->paginate(20);

        return $dispatch;
    }
}

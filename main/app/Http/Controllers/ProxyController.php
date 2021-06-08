<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proxy\CreateProxyRequest;
use App\Http\Requests\Proxy\UpdateProxyRequest;
use App\Models\Proxy;
use App\Models\User;
use App\Services\Proxy\Create\CreateProxyCommand;
use App\Services\Proxy\Delete\DeleteProxyCommand;
use App\Services\Proxy\Update\UpdateProxyCommand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class ProxyController.
 */
class ProxyController extends Controller
{
    /**
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request): LengthAwarePaginator
    {
        /** @var User $user */
        $user = $request->user();

        $proxy = Proxy::whereSellerId($user->seller_id);
        if ($ip = request('ip')) {
            $proxy->where('ip', 'like', "%$ip%");
        }

        if ($port = request('port')) {
            $proxy->where('port', 'like', "%$port%");
        }
        if ($type = request('type')) {
            $proxy->where('proxy_type', $type);
        }
        if (request('sortDirection') === 'asc') {
            $column = request('sortField');
            $proxy->orderBy($column);
        }
        if (request('sortDirection') === 'desc') {
            $column = request('sortField');
            $proxy->orderByDesc($column);
        }

        return $proxy->paginate(20);
    }

    /**
     * @param Request $request
     * @param Proxy   $proxy_number
     *
     * @return Proxy|Builder|Model
     */
    public function show(Request $request, Proxy $proxy_number)
    {
        /** @var User $user */
        $user = $request->user();

        return $proxy_number;
    }

    /**
     * @param CreateProxyRequest $request
     * @param CreateProxyCommand $command
     *
     * @return Proxy
     */
    public function store(CreateProxyRequest $request, CreateProxyCommand $command): Proxy
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateProxyRequest $request
     * @param UpdateProxyCommand $command
     * @param Proxy              $proxy_number
     *
     * @return Proxy
     */
    public function update(UpdateProxyRequest $request, UpdateProxyCommand $command, Proxy $proxy_number): Proxy
    {
        return $command->execute($proxy_number, $request->getDto());
    }

    /**
     * @param DeleteProxyCommand $command
     * @param Proxy              $proxy_number
     */
    public function destroy(DeleteProxyCommand $command, Proxy $proxy_number): void
    {
        $command->execute($proxy_number);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function forSelect(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return Proxy::where('seller_id', $user->seller_id)
            ->get()
            ->map(
                fn (Proxy $proxy) => [
                    'text'  => "{$proxy->ip}:{$proxy->port}",
                    'value' => $proxy->number,
                ]
            );
        // ->append(['text' => 'Все', 'value' => null]);
    }
}

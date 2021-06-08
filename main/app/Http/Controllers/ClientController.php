<?php

namespace App\Http\Controllers;

use App\Exports\ClientActualExport;
use App\Exports\ClientExport;
use App\Http\Requests\Client\MultiBanRequest;
use App\Http\Requests\Client\MultiBlackListRequest;
use App\Http\Requests\Client\MultiDeleteRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\ClientBanRequest;
use App\Models\Bot;
use App\Models\Client;
use App\Models\ClientHistory;
use App\Models\User;
use App\Services\Client\Ban\BanClientCommand;
use App\Services\Client\Ban\UnBanAllCommand;
use App\Services\Client\Ban\UnBanClientCommand;
use App\Services\Client\BlackList\BlackListClientHandle;
use App\Services\Client\Delete\DeleteClientCommand;
use App\Services\Client\MultiBan\MultiBanClientCommand;
use App\Services\Client\MultiBlackList\MultiBlackListClientCommand;
use App\Services\Client\MultiDelete\MultiDeleteClientCommand;
use App\Services\Client\SendMessageToClientCommand;
use App\Services\Client\Update\UpdateClientCommand;
use App\Services\Order\OrderCountQuery;
use App\Services\Order\VO\OrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ClientController.
 */
class ClientController extends Controller
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
        $client = Client::whereSellerId($user->seller_id)
            ->with([
                'orders', 'source', 'bots',
            ])->withCount([
                'orders as paid'           => function ($query) {
                    $query->where('orders.status', '=', OrderStatus::STATUS_PAID);
                },
                'orders as given_operator' => function ($query) {
                    $query->where('orders.status', '=', OrderStatus::STATUS_GIVEN);
                },
            ])
            ->dateFilter($request->get('created_at'))
            ->nameFilter($request->get('client'))
            ->discountFilter($request->get('discount_value'))
            ->noteFilter($request->get('note'))
            ->numberFilter($request->get('number'))
            ->blackListFilter($request->get('in_black_list'))
            ->bannedFilter($request->get('ban_expires_at'))
            ->botFilter($request->get('bots'))
            ->sortFilter(
                $request->get('sortDirection'),
                $request->get('sortField')
            )->paginate(20);

        $client->getCollection()->each(function (Client $client) {
            $client->append('coming');

            return $client;
        });

        return $client;
    }

    /**
     * @return Client[]|Builder[]|Collection
     */
    public function forSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return Client::whereSellerId($user->seller_id)
            ->get(['number', 'name', 'username', 'telegram_id'])
            ->append('label');
    }

    /**
     * @param OrderCountQuery $orderCountQuery
     * @param int             $id
     *
     * @return array
     */
    public function show(OrderCountQuery $orderCountQuery, int $id): array
    {
        /** @var User $user */
        $user = auth()->user();
        $client = Client::whereSellerId($user->seller_id)
            ->whereNumber($id)
            ->with([
                'orders',
                'source',
                'bots',
            ])
            ->firstOrFail()
            ->toArray();

        return array_merge($client, $orderCountQuery->execute($user->seller_id, $id));
    }

    /**
     * @param UpdateClientRequest $request
     * @param UpdateClientCommand $command
     * @param $id
     *
     * @return Client
     */
    public function update(UpdateClientRequest $request, UpdateClientCommand $command, $id): Client
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteClientCommand $command
     * @param $id
     *
     * @throws \Exception
     * @throws \Exception
     */
    public function destroy(DeleteClientCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param ClientBanRequest $request
     * @param BanClientCommand $command
     * @param Client           $client_number
     */
    public function banClient(ClientBanRequest $request, BanClientCommand $command, Client $client_number): void
    {
        $command->execute($request->getDto(), $client_number, $request->user());
    }

    /**
     * @param UnBanClientCommand $command
     * @param Client             $client_number
     */
    public function unBanClient(UnBanClientCommand $command, Client $client_number): void
    {
        $command->execute($client_number);
    }

    /**
     * @param BlackListClientHandle $handle
     * @param Client                $client_number
     */
    public function blackListClient(BlackListClientHandle $handle, Client $client_number): void
    {
        $handle->execute($client_number, true);
    }

    /**
     * @param BlackListClientHandle $handle
     * @param Client                $client_number
     */
    public function unBlackListClient(BlackListClientHandle $handle, Client $client_number): void
    {
        $handle->execute($client_number, false);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return BinaryFileResponse
     */
    public function exportToCsv(): BinaryFileResponse
    {
        /* @var User $user */
        $user = auth()->user();
        $fileName = now().'.csv';

        return Excel::download(new ClientExport($user->seller), $fileName, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
            'filename'     => $fileName,
        ]);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return BinaryFileResponse
     */
    public function exportCsvTelegramActualUsername(): BinaryFileResponse
    {
        /* @var User $user */
        $user = auth()->user();
        $fileName = now().'.csv';

        return Excel::download(new ClientActualExport($user->seller), $fileName, Excel::CSV, [
            'Content-Type' => 'text/csv',
            'filename'     => $fileName,
        ]);
    }

    public function unBanAll(UnBanAllCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();
        $command->execute($user->seller);
    }

    public function indexHandDispatchActualTelegram(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return Client::whereSellerId($user->seller_id)
            ->with('orders', 'lastOrder')
            ->whereHas(
                'orders',
                static fn (Builder $builder) => $builder
                ->whereIn('status', [
                    OrderStatus::STATUS_PAID,
                    OrderStatus::STATUS_GIVEN,
                ])
                ->whereBetween('created_at', [
                    now()->subMonths(3)->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])
            )
            ->whereNotNull('username')
            ->whereNull('ban_expires_at')
            ->where('in_black_list', 0)
            ->withCount([
                'orders as paid' => static function ($query) {
                    $query->where('orders.status', '=', OrderStatus::STATUS_PAID);
                },
            ])
            ->when(request('name'), fn (Builder $builder) => $builder
                ->where('name', 'like', '%'.\request('name').'%')
                ->orWhere('username', 'like', '%'.\request('name').'%'))
            ->when(request('sortDirection'), fn (Builder $builder) => $builder
                ->orderBy(\request('sortField'), request('sortDirection')))->paginate(20);
    }

    public function spamReserve(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return Client::whereSellerId($user->seller_id)
            ->with('orders')
            ->whereHas('orders', fn (Builder $builder) => $builder
                ->whereIn('status', OrderStatus::CANCELED_STATUS))
            ->withCount([
                'orders as count_canceled' => fn (Builder $builder) => $builder
                    ->whereIn('status', OrderStatus::CANCELED_STATUS),
            ])
            ->when(
                \request('period_cancle'),
                fn (Builder $builder) => $builder
                ->whereHas(
                    'orders',
                    fn (Builder $builder) => $builder
                    ->whereBetween('canceled_at', [
                        now()->subHours(request('period_cancle'))->toDateString(),
                        now()->toDateTimeString(),
                    ])
                )
            )
            ->when(
                request('spam_reserve_cancel_count'),
                fn (Builder $builder) => $builder
                ->having('count_canceled', '<=', request('spam_reserve_cancel_count'))
            )
            ->when(
                request('period_add_client'),
                fn (Builder $builder) => $builder
                ->whereBetween('created_at', [
                    now()->subHours(request('period_add_client'))->toDateString(),
                    now()->toDateTimeString(),
                ])
            )->paginate(20);
    }

    public function multiBan(MultiBanRequest $request, MultiBanClientCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();
        $command->execute($request->getDto(), $user->seller);
    }

    public function multiBlackList(MultiBlackListRequest $request, MultiBlackListClientCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();
        $command->execute($request->getDto(), $user->seller);
    }

    public function multiDelete(MultiDeleteRequest $request, MultiDeleteClientCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();
        $command->execute($request->getDto(), $user->seller);
    }

    public function sendMessageToClient(SendMessageToClientCommand $command): void
    {
        /* @var User $user */
        $user = auth()->user();

        $client = Client::whereSellerId($user->seller_id)->whereNumber(\request('number'))->firstOrFail();
        $bot = Bot::whereNumber(\request('bot_number'))->first();

        $message = \request('message');

        $photos = \request('photos');

        if (!$message || !$bot) {
            return;
        }

        $command->execute($client, $bot, $message, $photos);
    }

    /**
     * @param Request $request
     *
     * @return ClientHistory[]|Builder[]|Collection
     */
    public function getHistory(Request $request)
    {
        /* @var User $user */
        $user = auth()->user();

        $sellerNumber = $request->get('number');

        $client = Client::whereSellerId($user->seller_id)->whereNumber($sellerNumber)->firstOrFail();

        return ClientHistory::where('client_id', $client->id)->get();
    }
}

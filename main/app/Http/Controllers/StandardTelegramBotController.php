<?php

namespace App\Http\Controllers;

use App\Bot\Commands\WebhookReinstallCommand;
use App\Http\Requests\StandardTelegramBot\CreateRequest;
use App\Http\Requests\StandardTelegramBot\UpdateRequest;
use App\Models\Bot;
use App\Models\User;
use App\Services\Bot\Create\CreateStandardTelegramBotCommand;
use App\Services\Bot\Delete\DeleteStandardTelegramBotCommand;
use App\Services\Bot\Update\UpdateStandardTelegramBotCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class StandardTelegramBotController.
 */
class StandardTelegramBotController extends Controller
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
        $bots = Bot::standardTelegram()
            ->with('operator.client', 'drivers.client')
            ->whereSellerId($user->seller_id)
            ->numberFilter($request->get('number'))
            ->nameFilter($request->get('name'))
            ->operatorFilter($request->get('operator'))
            ->driverFilter($request->get('drivers'))
            ->sortFilter($request->get('sortDirection'), $request->get('sortField'));

        return $bots
            ->with('logic')
            ->paginate(20);
    }

    /**
     * @param $id
     *
     * @return Bot|Builder|Model
     */
    public function show($id)
    {
        /** @var User $user */
        $user = auth()->user();

        return Bot::standardTelegram()
            ->with('operator.client', 'drivers.client')
            ->whereSellerId($user->seller_id)
            ->whereNumber($id)
            ->with('logic')
            ->firstOrFail()
            ->append('driver_numbers');
    }

    public function store(CreateRequest $request, CreateStandardTelegramBotCommand $command): Bot
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute($user->seller, $request->getDto());
    }

    /**
     * @param UpdateRequest                    $request
     * @param UpdateStandardTelegramBotCommand $command
     * @param $id
     *
     * @return Bot
     */
    public function update(UpdateRequest $request, UpdateStandardTelegramBotCommand $command, $id): Bot
    {
        /** @var User $user */
        $user = auth()->user();

        return $command->execute((int) $id, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteStandardTelegramBotCommand $command
     * @param $id
     */
    public function destroy(DeleteStandardTelegramBotCommand $command, $id): void
    {
        /** @var User $user */
        $user = auth()->user();

        $command->execute((int) $id, $user->seller);
    }

    /**
     * @param WebhookReinstallCommand $webhookReinstallCommand
     * @param $number
     *
     * @return array
     */
    public function reinstallWebhook(WebhookReinstallCommand $webhookReinstallCommand, $number): array
    {
        $user = auth()->user();

        return $webhookReinstallCommand->execute($number, $user->seller_id);
    }

    /**
     * @return Bot[]|Builder[]|Collection
     */
    public function getSelect()
    {
        /** @var User $user */
        $user = auth()->user();

        return Bot::standardTelegram()
            ->when(
                $clientNumber = request('client_number'),
                fn (Builder $q) => $q
                ->whereHas(
                    'clients',
                    fn (Builder $q) => $q
                    ->where('number', $clientNumber)
                )
            )
            ->whereSellerId($user->seller_id)->get(['username as text', 'number as value']);
    }
}

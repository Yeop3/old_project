<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\BotMessage;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class MessagesController.
 */
class MessagesController extends Controller
{
    public function index(Request $request, Client $client_number, Bot $bot_number): LengthAwarePaginator
    {
        $dateStart = carbonSafeParse($request->get('date_start'));
        $dateEnd = carbonSafeParse($request->get('date_end'));

        return BotMessage::query()
            ->when(
                $bot_number,
                fn (Builder $builder, Bot $bot) => $builder->where('bot_id', $bot_number->id)
            )
            ->when(
                $client_number,
                fn (Builder $builder, Client $client) => $builder->where(
                    'client_id',
                    $client_number->id
                )
            )
            ->when(
                $dateStart,
                fn (Builder $builder) => $builder
                    ->whereDate('created_at', '>=', $dateStart)
                    ->whereTime('created_at', '>=', $dateStart)
            )
            ->when(
                $dateEnd,
                fn (Builder $builder) => $builder
                    ->whereDate('created_at', '<', $dateEnd)
                    ->whereTime('created_at', '<', $dateEnd)
            )->paginate(50);
    }
}

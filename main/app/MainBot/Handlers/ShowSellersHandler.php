<?php

declare(strict_types=1);

namespace App\MainBot\Handlers;

use App\Models\MainBot;
use App\Models\Seller;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ShowSellersHandler.
 */
final class ShowSellersHandler implements Handler
{
    private BotMan $botMan;

    public function execute(BotMan $botMan, MainBot $botModel, ...$params): void
    {
        $this->botMan = $botMan;

        $availableSellers = $this->getAvailableSellers();

        if (!$availableSellers) {
            $this->handleSellersNotFound();

            return;
        }

        $this->handle($availableSellers);
    }

    /**
     * @return Seller[]|Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    private function getAvailableSellers()
    {
        return Seller::whereHas('bots', fn ($q) => $q->whereActive(true))->with('bots')->get()->map(function ($seller) {
            return $seller->name.' @'.$seller->bots->first()->username;
        });
    }

    private function handleSellersNotFound(): void
    {
        $template = config('main_bot_logic.standart.commands.list_sellers_not_found.content');

        $this->botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $template),
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * @param $availableSellers
     */
    private function handle($availableSellers): void
    {
        $template = config('main_bot_logic.standart.commands.list_sellers.content');

        $dilimeter = "\n➖➖➖➖➖➖➖➖➖➖➖\n";
        $this->botMan->reply(
            str_replace('{list}', $dilimeter.$availableSellers->join($dilimeter).$dilimeter, $template),
            ['parse_mode' => 'HTML']
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Bot;

use App\Models\Bot;
use App\Models\Seller;
use App\Services\Bot\Exceptions\BotNameExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkNameDuplicate(Seller $seller, string $name, ?int $exceptNumber = null): void
    {
        $botExists = Bot::whereSellerId($seller->id)
            ->whereName($name)
            ->when($exceptNumber, fn (Builder $query) => $query->where('number', '!=', $exceptNumber))
            ->exists();

        if ($botExists) {
            throw new BotNameExistsException("Bot with name $name already exists");
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services\MainBot;

use App\Models\MainBot;
use App\Services\Bot\Exceptions\BotNameExistsException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Checker.
 */
final class Checker
{
    public function checkNameDuplicate(string $name, ?int $exceptId = null): void
    {
        $driverExists = MainBot::whereName($name)
            ->when($exceptId, fn (Builder $query) => $query->where('id', '!=', $exceptId))
            ->exists();

        if ($driverExists) {
            throw new BotNameExistsException("Bot with name $name already exists");
        }
    }
}

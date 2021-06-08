<?php

declare(strict_types=1);

namespace App\Services\Coordinates\Parsers;

use App\Services\Coordinates\Coordinates;
use Illuminate\Support\Str;

/**
 * example: https://maps.google.com/maps?q=46.405180,30.728319&ll=46.405180,30.728319&z=16
 * when there is "46.405180,30.728319" coordinates.
 */
final class SimpleCoordinatesParser implements CoordinatesParser
{
    /**
     * @param string $coordinates
     *
     * @return string|null
     */
    public function parse(string $coordinates): ?string
    {
        $coordinates = Str::replaceArray(' ', [''], $coordinates);

        preg_match(Coordinates::REGEX, $coordinates, $matches);

        return $matches[0] ?? null;
    }
}

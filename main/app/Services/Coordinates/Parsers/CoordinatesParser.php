<?php

declare(strict_types=1);

namespace App\Services\Coordinates\Parsers;

/**
 * Interface CoordinatesParser.
 */
interface CoordinatesParser
{
    public function parse(string $coordinates): ?string;
}

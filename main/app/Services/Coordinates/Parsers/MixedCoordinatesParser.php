<?php

declare(strict_types=1);

namespace App\Services\Coordinates\Parsers;

/**
 * Class MixedCoordinatesParser.
 */
final class MixedCoordinatesParser implements CoordinatesParser
{
    /**
     * @var CoordinatesParser[]
     */
    private array $parsers;

    public function __construct()
    {
        $this->parsers = [
            new SimpleCoordinatesParser(),
        ];
    }

    public function parse(string $coordinates): ?string
    {
        foreach ($this->parsers as $parser) {
            $parsed = $parser->parse($coordinates);

            if ($parsed) {
                return $parsed;
            }
        }

        return null;
    }
}

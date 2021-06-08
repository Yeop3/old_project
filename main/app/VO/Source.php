<?php

declare(strict_types=1);

namespace App\VO;

/**
 * Class Source.
 */
final class Source
{
    private int        $id;
    private SourceType $type;

    public function __construct(int $id, SourceType $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): SourceType
    {
        return $this->type;
    }
}

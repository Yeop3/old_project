<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class BotLogicAntispamDto.
 */
final class BotLogicAntispamDto
{
    private string $key;
    /**
     * @var BotLogicOptionDto[]
     */
    private array $options;

    /**
     * BotLogicAntispamDto constructor.
     *
     * @param string              $key
     * @param BotLogicOptionDto[] $options
     */
    public function __construct(string $key, array $options)
    {
        $this->key = $key;
        $this->options = $options;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

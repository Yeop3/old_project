<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class BotLogicEventDto.
 */
final class BotLogicEventDto
{
    private string $key;
    private string $content;

    public function __construct(string $key, string $content)
    {
        $this->key = $key;
        $this->content = $content;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

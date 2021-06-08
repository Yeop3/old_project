<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class BotLogicReminderDto.
 */
final class BotLogicReminderDto
{
    private string $key;
    /**
     * @var BotLogicOptionDto[]
     */
    private array $options;

    /**
     * BotLogicReminderDto constructor.
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

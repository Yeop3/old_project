<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class BotLogicCommandDto.
 */
final class BotLogicCommandDto
{
    private array $keys;
    /**
     * @var BotLogicCommandTemplateDto[]
     */
    private array $templates;

    /**
     * BotLogicCommandDto constructor.
     *
     * @param array                        $keys
     * @param BotLogicCommandTemplateDto[] $templates
     */
    public function __construct(array $keys, array $templates)
    {
        $this->keys = $keys;
        $this->templates = $templates;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class BotLogicOptionDto.
 */
final class BotLogicOptionDto
{
    private string $key;
    private $value;

    /**
     * BotLogicOptionDto constructor.
     *
     * @param string $key
     * @param $value
     */
    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'key'   => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }
}

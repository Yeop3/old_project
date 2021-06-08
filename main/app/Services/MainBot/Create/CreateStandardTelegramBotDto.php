<?php

declare(strict_types=1);

namespace App\Services\MainBot\Create;

/**
 * Class CreateStandardTelegramBotDto.
 */
final class CreateStandardTelegramBotDto
{
    private string $name;
    private string $token;
    private bool $active;

    public function __construct(string $name, string $token, bool $active)
    {
        $this->name = $name;
        $this->token = $token;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}

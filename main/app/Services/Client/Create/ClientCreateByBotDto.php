<?php

declare(strict_types=1);

namespace App\Services\Client\Create;

/**
 * Class ClientCreateByBotDto.
 */
final class ClientCreateByBotDto
{
    private ?string $name;
    private array $info;
    private int $telegramId;
    private ?string $username;

    public function __construct(int $telegramId, array $info, ?string $name = null, ?string $username = null)
    {
        $this->name = $name;
        $this->info = $info;
        $this->telegramId = $telegramId;
        $this->username = $username;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function getTelegramId(): int
    {
        return $this->telegramId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}

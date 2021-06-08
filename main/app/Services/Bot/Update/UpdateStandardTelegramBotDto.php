<?php

declare(strict_types=1);

namespace App\Services\Bot\Update;

use App\Services\Bot\VO\BotLogicType;

/**
 * Class UpdateStandardTelegramBotDto.
 */
final class UpdateStandardTelegramBotDto
{
    private string $name;
    private int $logicNumber;
    private BotLogicType $logicType;
    private bool $active;
    private bool $allowCreateClients;
    private ?int $operatorNumber;
    private array $driverNumbers;

    public function __construct(string $name, int $logicNumber, BotLogicType $logicType, bool $active, bool $allowCreateClients, ?int $operatorNumber = null, array $driverNumbers = [])
    {
        $this->name = $name;
        $this->logicNumber = $logicNumber;
        $this->logicType = $logicType;
        $this->active = $active;
        $this->allowCreateClients = $allowCreateClients;
        $this->operatorNumber = $operatorNumber;
        $this->driverNumbers = $driverNumbers;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogicNumber(): int
    {
        return $this->logicNumber;
    }

    public function getLogicType(): BotLogicType
    {
        return $this->logicType;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isAllowCreateClients(): bool
    {
        return $this->allowCreateClients;
    }

    public function getOperatorNumber(): ?int
    {
        return $this->operatorNumber;
    }

    public function getDriverNumbers(): array
    {
        return $this->driverNumbers;
    }
}

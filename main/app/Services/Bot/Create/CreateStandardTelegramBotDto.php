<?php

declare(strict_types=1);

namespace App\Services\Bot\Create;

use App\Services\Bot\VO\BotLogicType;

/**
 * Class CreateStandardTelegramBotDto.
 */
final class CreateStandardTelegramBotDto
{
    private string $name;
    private string $token;
    private int $logicNumber;
    private BotLogicType $logicType;
    private bool $active;
    private bool $allowCreateClients;
    /** @var int|null */
    private ?int $operatorNumber;
    private array $driverNumbers;

    public function __construct(
        string $name,
        string $token,
        int $logicNumber,
        BotLogicType $logicType,
        bool $active,
        bool $allowCreateClients,
        ?int $operatorNumber = null,
        array $driverNumbers = []
    ) {
        $this->name = $name;
        $this->token = $token;
        $this->logicNumber = $logicNumber;
        $this->logicType = $logicType;
        $this->active = $active;
        $this->allowCreateClients = $allowCreateClients;
        $this->operatorNumber = $operatorNumber;
        $this->driverNumbers = $driverNumbers;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getLogicNumber(): int
    {
        return $this->logicNumber;
    }

    /**
     * @return BotLogicType
     */
    public function getLogicType(): BotLogicType
    {
        return $this->logicType;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isAllowCreateClients(): bool
    {
        return $this->allowCreateClients;
    }

    /**
     * @return int|null
     */
    public function getOperatorNumber(): ?int
    {
        return $this->operatorNumber;
    }

    /**
     * @return array
     */
    public function getDriverNumbers(): array
    {
        return $this->driverNumbers;
    }
}

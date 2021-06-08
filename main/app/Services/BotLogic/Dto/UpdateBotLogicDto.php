<?php

declare(strict_types=1);

namespace App\Services\BotLogic\Dto;

/**
 * Class UpdateBotLogicDto.
 */
final class UpdateBotLogicDto
{
    private string $name;
    private string $description;
    /**
     * @var BotLogicCommandDto[]
     */
    private array $commands;
    /**
     * @var BotLogicEventDto[]
     */
    private array $events;
    /**
     * @var BotLogicOperatorNotificationDto[]
     */
    private array $operatorNotifications;
    /**
     * @var BotLogicAntispamDto[]
     */
    private array $antispams;
    /**
     * @var BotLogicReminderDto[]
     */
    private array $reminders;
    /**
     * @var BotLogicDistributionDto[]
     */
    private array $distributions;

    /**
     * UpdateBotLogicDto constructor.
     *
     * @param string                            $name
     * @param string                            $description
     * @param BotLogicCommandDto[]              $commands
     * @param BotLogicEventDto[]                $events
     * @param BotLogicOperatorNotificationDto[] $operatorNotifications
     * @param BotLogicAntispamDto[]             $antispams
     * @param BotLogicReminderDto[]             $reminders
     * @param BotLogicDistributionDto[]         $distributions
     */
    public function __construct(
        string $name,
        string $description,
        array $commands,
        array $events,
        array $operatorNotifications,
        array $antispams,
        array $reminders,
        array $distributions
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->commands = $commands;
        $this->events = $events;
        $this->operatorNotifications = $operatorNotifications;
        $this->antispams = $antispams;
        $this->reminders = $reminders;
        $this->distributions = $distributions;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getOperatorNotifications(): array
    {
        return $this->operatorNotifications;
    }

    public function getAntispams(): array
    {
        return $this->antispams;
    }

    public function getReminders(): array
    {
        return $this->reminders;
    }

    public function getDistributions(): array
    {
        return $this->distributions;
    }
}

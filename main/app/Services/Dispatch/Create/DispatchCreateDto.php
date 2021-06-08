<?php

declare(strict_types=1);

namespace App\Services\Dispatch\Create;

/**
 * Class DispatchCreateDto.
 */
final class DispatchCreateDto
{
    private array $botsNumbers;
    private string $messages;

    /**
     * DispatchCreateDto constructor.
     *
     * @param array  $botsNumbers
     * @param string $messages
     */
    public function __construct(array $botsNumbers, string $messages)
    {
        $this->botsNumbers = $botsNumbers;
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getBotsNumbers(): array
    {
        return $this->botsNumbers;
    }

    /**
     * @return string
     */
    public function getMessages(): string
    {
        return $this->messages;
    }
}

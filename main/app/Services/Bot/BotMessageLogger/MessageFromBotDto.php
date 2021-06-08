<?php

declare(strict_types=1);

namespace App\Services\Bot\BotMessageLogger;

/**
 * Class MessageFromBotDto.
 */
final class MessageFromBotDto
{
    private array $photoUrls;
    private array $videoUrls;
    private ?string $location;
    private ?string $text;

    /**
     * MessageFromBotDto constructor.
     *
     * @param string|null $text
     * @param array       $photoUrls
     * @param array       $videoUrls
     * @param string|null $location
     */
    public function __construct(?string $text, array $photoUrls, array $videoUrls, ?string $location)
    {
        $this->text = $text;
        $this->photoUrls = $photoUrls;
        $this->videoUrls = $videoUrls;
        $this->location = $location;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return array
     */
    public function getPhotoUrls(): array
    {
        return $this->photoUrls;
    }

    /**
     * @return array
     */
    public function getVideoUrls(): array
    {
        return $this->videoUrls;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }
}

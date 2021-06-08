<?php

declare(strict_types=1);

namespace App\Services\Bot\BotMessageLogger;

use App\Models\Client;

/**
 * Class LogMessageFromBot.
 */
final class LogMessageFromBot
{
    private BotMessageLogger $botMessageLogger;

    public function __construct(BotMessageLogger $botMessageLogger)
    {
        $this->botMessageLogger = $botMessageLogger;
    }

    /**
     * @param Client            $client
     * @param MessageFromBotDto $dto
     */
    public function __invoke(Client $client, MessageFromBotDto $dto)
    {
        $text = $dto->getText() ?? '';

        if (count($dto->getPhotoUrls()) > 0) {
            $text .= "\n Прикрепленные картинки:";

            foreach ($dto->getPhotoUrls() as $photoUrl) {
                $text .= "\n $photoUrl";
            }

            $text .= "\n";
        }

        if (count($dto->getVideoUrls()) > 0) {
            $text .= "\n Прикрепленные видео:";

            foreach ($dto->getVideoUrls() as $videoUrl) {
                $text .= "\n $videoUrl";
            }

            $text .= "\n";
        }

        if ($dto->getLocation()) {
            $text .= "\n Прикрепленная локация:";

            $text .= "\n {$dto->getLocation()}\n";
        }

        $this->botMessageLogger->log($client, $text, true);
    }
}

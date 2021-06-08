<?php

declare(strict_types=1);

namespace App\Services\Bot\BotMessageLogger;

use App\Models\Client;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use UnexpectedValueException;

/**
 * Class LogMessageFromClient.
 */
final class LogMessageFromClient
{
    private BotMessageLogger $botMessageLogger;

    public function __construct(BotMessageLogger $botMessageLogger)
    {
        $this->botMessageLogger = $botMessageLogger;
    }

    /**
     * @param Client          $client
     * @param IncomingMessage $incomingMessage
     */
    public function __invoke(Client $client, IncomingMessage $incomingMessage)
    {
        if ($incomingMessage->getPayload()['caption'] ?? null) {
            $text = $incomingMessage->getPayload()['caption'];
        } else {
            $text = $incomingMessage->getText();
        }

        $text = str_replace(
            ['%%%_IMAGE_%%%', '%%%_VIDEO_%%%', '%%%_LOCATION_%%%', '%%%_AUDIO_%%%'],
            '',
            $text
        );
        $text = trim($text);

        if (count($incomingMessage->getImages()) > 0) {
            $text .= "\nПрикрепленные картинки:";

            /** @var Image $image */
            foreach ($incomingMessage->getImages() as $image) {
                $text .= "\n{$image->getUrl()}";
            }

            $text .= "\n";
        }

        if (count($incomingMessage->getVideos()) > 0) {
            $text .= "\nПрикрепленные видео:";

            /** @var Video $video */
            foreach ($incomingMessage->getVideos() as $video) {
                $text .= "\n{$video->getUrl()}";
            }

            $text .= "\n";
        }

        if (count($incomingMessage->getFiles()) > 0) {
            $text .= "\nПрикрепленные файлы:";

            /** @var File $video */
            foreach ($incomingMessage->getFiles() as $file) {
                $text .= "\n{$file->getUrl()}";
            }

            $text .= "\n";
        }

        try {
            if ($incomingMessage->getLocation()) {
                $text .= "\nПрикрепленная локация:";

                $text .= "\n{$incomingMessage->getLocation()->getLatitude()}, {$incomingMessage->getLocation()->getLongitude()}\n";
            }
        } catch (UnexpectedValueException $e) {
            //throw $e;
        }

        try {
            if (count($incomingMessage->getAudio()) > 0) {
                $text .= "\nПрикрепленные аудиозаписи:";

                /** @var Audio $video */
                foreach ($incomingMessage->getAudio() as $audio) {
                    $text .= "\n{$audio->getUrl()}";
                }

                $text .= "\n";
            }
        } catch (UnexpectedValueException $e) {

            //throw $e;
        }

        $this->botMessageLogger->log($client, $text, false);
    }
}

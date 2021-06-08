<?php

declare(strict_types=1);

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ConversationHelpers.
 */
final class ConversationHelpers
{
    public const CANCEL_VALUE = 'cancel';
    public const SKIP_VALUE = 'skip';
    public const CANCEL_MESSAGE = 'Отменить создание';

    /**
     * @param array $array
     * @param array $rules
     *
     * @return array
     */
    public static function createButtons($array = null, $rules = null): array
    {
        $buttons = [];
        $array = $array ?? [];
        $rules = $rules ?? [];
        foreach ($array as $key => $name) {
            $buttons[] = Button::create($name)->value($key);
        }

        if (self::isCanSkipAsk($rules)) {
            $buttons[] = self::skipAskButton();
        }

        $buttons[] = self::cancelButton();

        return $buttons;
    }

    /**
     * @param array $rules
     *
     * @return bool
     */
    public static function isCanSkipAsk(array $rules): bool
    {
        return in_array('nullable', $rules, true);
    }

    /**
     * @return Button
     */
    public static function skipAskButton(): Button
    {
        return Button::create('Пропустить')->value(self::SKIP_VALUE);
    }

    /**
     * @return Button
     */
    public static function cancelButton(): Button
    {
        return Button::create(self::CANCEL_MESSAGE)->value(self::CANCEL_VALUE);
    }

    /**
     * @param Answer $answer
     *
     * @return bool
     */
    public static function isCancel(Answer $answer): bool
    {
        return $answer->isInteractiveMessageReply() && $answer->getValue() === self::CANCEL_VALUE;
    }

    /**
     * @param Answer $answer
     *
     * @return bool
     */
    public static function isSkiped(Answer $answer): bool
    {
        return $answer->isInteractiveMessageReply() && $answer->getValue() === self::SKIP_VALUE;
    }

    /**
     * @param $fileUrl
     *
     * @return string
     */
    public static function saveTempFile($fileUrl): string
    {
        $extension = pathinfo(
            parse_url(
                trim($fileUrl),
                PHP_URL_PATH
            ),
            PATHINFO_EXTENSION
        );
        $file = file_get_contents($fileUrl);
        $name = Str::random(20);
        $path = 'temp/'.$name.'.'.$extension;
        Storage::put($path, $file);

        return $path;
    }

    /**
     * @param $filePath
     *
     * @return bool
     */
    public static function removeTempFile($filePath): bool
    {
        return Storage::exists($filePath) ? Storage::delete($filePath) : false;
    }
}

<?php

declare(strict_types=1);

namespace App\Bot\Handlers;

use App\Models\Bot;
use App\Models\BotLogic\BotLogicCommand;
use BotMan\BotMan\BotMan;

/**
 * Class MenuHandler.
 */
final class MenuHandler implements BotHandler
{
    /**
     * @param BotMan      $botMan
     * @param Bot         $botModel
     * @param string|null ...$params
     */
    public function execute(BotMan $botMan, Bot $botModel, ?string ...$params): void
    {
        $botLogic = $botModel->logic;

        $command = BotLogicCommand::whereBotLogicId($botLogic->id)
            ->where('keys', 'like', '%/menu%')
            ->with('templates')->firstOrFail();

        $template = $command->templates->where('key', 'menu')->first();

        $operator = $botModel->operator;

        $lastUserName = null;

        if ($operator && $operator->client) {
            if ($operator->client->history->count()) {
                $lastUserName = $operator->client->history->sortByDesc('created_at')->first()->username;
            } else {
                $lastUserName = $operator->client->username;
            }
        }

        $content = str_replace([
            '{operator}',
            '{bot_name}',
        ], [
            $lastUserName ? "@{$lastUserName}" : '',
            $botModel->name,
        ], $template->content);

        $botMan->reply(
            str_replace(['\\\\r\\\\n', '\\r\\n'], "\n", $content),
            ['parse_mode' => 'HTML']
        );
    }
}

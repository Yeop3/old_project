<?php

declare(strict_types=1);

namespace App\Services\BotLogic;

use App\Models\BotLogic\BotLogic;
use App\Models\BotLogic\BotLogicAntispam;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\BotLogic\BotLogicCommandTemplate;
use App\Models\BotLogic\BotLogicDistribution;
use App\Models\BotLogic\BotLogicEvent;
use App\Models\BotLogic\BotLogicOperatorNotification;
use App\Models\BotLogic\BotLogicReminder;
use App\Models\Seller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateBotLogicFromConfigCommand.
 */
final class CreateBotLogicFromConfigCommand
{
    public function execute(string $type, ?Seller $seller = null): BotLogic
    {
        $botLogicConfig = config('bot_logic.'.$type);

        if (!$botLogicConfig) {
            throw new \RuntimeException('unknown bot logic type');
        }

        $botLogic = BotLogic::whereSellerId(optional($seller)->id)->whereName($botLogicConfig['name'])->first();

        if ($botLogic) {
            return $botLogic;
        }

        $botLogic = new BotLogic();

        $botLogic->seller_id = optional($seller)->id;
        $botLogic->name = $botLogicConfig['name'];
        $botLogic->description = $botLogicConfig['description'];

        DB::beginTransaction();

        $botLogic->save();

        $this->createCommands($botLogicConfig, $botLogic);

        $this->createEvents($botLogicConfig, $botLogic);

        $this->createNotifications($botLogicConfig, $botLogic);

        $this->createAntispam($botLogicConfig, $botLogic);

        $this->createReminders($botLogicConfig, $botLogic);

        $this->createDistributions($botLogicConfig, $botLogic);

        DB::commit();

        return $botLogic;
    }

    private function handleContent(string $content): string
    {
        return preg_replace('#\s{2,}#ui', "\n", $content);
    }

    private function createCommands(array $botLogicConfig, BotLogic $botLogic): void
    {
        foreach ($botLogicConfig['commands'] as $command) {
            $botLogicCommand = new BotLogicCommand();
            $botLogicCommand->bot_logic_id = $botLogic->id;
            $botLogicCommand->keys = $command['keys'];
            $botLogicCommand->save();

            foreach ($command['templates'] as $template) {
                $botLogicCommandTemplate = new BotLogicCommandTemplate();
                $botLogicCommandTemplate->bot_logic_command_id = $botLogicCommand->id;
                $botLogicCommandTemplate->key = $template['key'];
                $botLogicCommandTemplate->content = $this->handleContent($template['content']);
                $botLogicCommandTemplate->save();
            }
        }
    }

    private function createEvents(array $botLogicConfig, BotLogic $botLogic): void
    {
        if (empty($botLogicConfig['events'])) {
            return;
        }
        foreach ($botLogicConfig['events'] as $event) {
            $botLogicEvent = new BotLogicEvent();
            $botLogicEvent->bot_logic_id = $botLogic->id;
            $botLogicEvent->key = $event['key'];
            $botLogicEvent->content = $this->handleContent($event['content']);
            $botLogicEvent->save();
        }
    }

    private function createNotifications(array $botLogicConfig, BotLogic $botLogic): void
    {
        if (empty($botLogicConfig['operator_notifications'])) {
            return;
        }
        foreach ($botLogicConfig['operator_notifications'] as $notification) {
            $botLogicNotification = new BotLogicOperatorNotification();
            $botLogicNotification->bot_logic_id = $botLogic->id;
            $botLogicNotification->key = $notification['key'];
            $botLogicNotification->content = $this->handleContent($notification['content']);
            $botLogicNotification->save();
        }
    }

    private function createAntispam(array $botLogicConfig, BotLogic $botLogic): void
    {
        if (empty($botLogicConfig['antispam'])) {
            return;
        }
        foreach ($botLogicConfig['antispam'] as $antispam) {
            $botLogicAntispam = new BotLogicAntispam();
            $botLogicAntispam->bot_logic_id = $botLogic->id;
            $botLogicAntispam->key = $antispam['key'];

            $botLogicAntispam->options = $this->handleOptions($antispam['options']);

            $botLogicAntispam->save();
        }
    }

    private function createReminders(array $botLogicConfig, BotLogic $botLogic): void
    {
        if (empty($botLogicConfig['reminders'])) {
            return;
        }
        foreach ($botLogicConfig['reminders'] as $reminder) {
            $botLogicReminder = new BotLogicReminder();
            $botLogicReminder->bot_logic_id = $botLogic->id;
            $botLogicReminder->key = $reminder['key'];

            $botLogicReminder->options = $this->handleOptions($reminder['options']);

            $botLogicReminder->save();
        }
    }

    private function createDistributions(array $botLogicConfig, BotLogic $botLogic): void
    {
        if (empty($botLogicConfig['distribution'])) {
            return;
        }
        foreach ($botLogicConfig['distribution'] as $distribution) {
            $botLogicDistribution = new BotLogicDistribution();
            $botLogicDistribution->bot_logic_id = $botLogic->id;
            $botLogicDistribution->key = $distribution['key'];
            $botLogicDistribution->content = $this->handleContent($distribution['content']);
            $botLogicDistribution->save();
        }
    }

    /**
     * @param $options
     *
     * @return array
     */
    private function handleOptions($options): array
    {
        return collect($options)
            ->map(function ($option) {
                if (is_string($option['value'])) {
                    $option['value'] = $this->handleContent($option['value']);
                }

                return Arr::only($option, ['key', 'value']);
            })
            ->toArray();
    }
}

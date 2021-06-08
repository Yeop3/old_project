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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class RefreshBotLogic.
 */
final class RefreshBotLogic
{
    /**
     * @var UpdateBotLogicCommand
     */
    private UpdateBotLogicCommand $updateBotLogicCommand;

    /**
     * RefreshBotLogic constructor.
     *
     * @param UpdateBotLogicCommand $updateBotLogicCommand
     */
    public function __construct(UpdateBotLogicCommand $updateBotLogicCommand)
    {
        $this->updateBotLogicCommand = $updateBotLogicCommand;
    }

    /**
     * @param null $update
     *
     * @throws Throwable
     */
    public function execute($update = null): void
    {
        $update = $update ?? 'all';
        $botLogicConfig = config('bot_logic.standard');

        BotLogic::with('seller')
            ->when($update === 'standard', fn (Builder $builder) => $builder->whereNull('seller_id'))
            ->chunk(100, function (Collection $collection) use ($botLogicConfig) {
                $collection->each(function (BotLogic $botLogic) use ($botLogicConfig) {
                    DB::beginTransaction();

//                    $botLogic->commands()->delete();
//                    $botLogic->events()->delete();
//                    $botLogic->operatorNotifications()->delete();
//                    $botLogic->antispams()->delete();
//                    $botLogic->reminders()->delete();
//                    $botLogic->distributions()->delete();

//                    $botLogic->save();

                    $this->createCommands($botLogicConfig, $botLogic);

                    $this->createEvents($botLogicConfig, $botLogic);

                    $this->createNotifications($botLogicConfig, $botLogic);

                    $this->createAntispam($botLogicConfig, $botLogic);

                    $this->createReminders($botLogicConfig, $botLogic);

                    $this->createDistributions($botLogicConfig, $botLogic);

                    DB::commit();
                });
            });
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function handleContent(string $content): string
    {
        return preg_replace('#\\s{2,}#ui', "\n", $content);
    }

    /**я
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
    private function createCommands(array $botLogicConfig, BotLogic $botLogic): void
    {
        foreach ($botLogicConfig['commands'] as $command) {
                if (!BotLogicCommand::whereBotLogicId($botLogic->id)->whereJsonContains('keys', $command['keys'])->exists()) {
                    $botLogicCommand = new BotLogicCommand();
                    $botLogicCommand->bot_logic_id = $botLogic->id;
                    $botLogicCommand->keys = $command['keys'];
                    $botLogicCommand->save();

                foreach ($command['templates'] as $template) {
                    if (!BotLogicCommandTemplate::whereBotLogicCommandId($botLogicCommand->id)->where('key', $template['key'])->exists()) {
                        $botLogicCommandTemplate = new BotLogicCommandTemplate();
                        $botLogicCommandTemplate->bot_logic_command_id = $botLogicCommand->id;
                        $botLogicCommandTemplate->key = $template['key'];
                        $botLogicCommandTemplate->content = $this->handleContent($template['content']);
                        $botLogicCommandTemplate->save();
                    }
                }
            }
        }
    }

    /**
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
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

    /**
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
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

    /**
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
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

    /**
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
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

    /**
     * @param array    $botLogicConfig
     * @param BotLogic $botLogic
     */
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

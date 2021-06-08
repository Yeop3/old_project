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
use Illuminate\Support\Facades\DB;

/**
 * Class CloneBotLogicCommand.
 */
final class CloneBotLogicCommand
{
    public function execute(BotLogic $botLogic, Seller $seller): BotLogic
    {
        $botLogic->loadMissing([
            'commands.templates',
            'events',
            'operatorNotifications',
            'antispams',
            'reminders',
            'distributions',
        ]);

        $clonedBotLogic = new BotLogic();

        $time = $botLogic->created_at->toDateTimeString();

        $clonedBotLogic->seller_id = $seller->id;
        $clonedBotLogic->name = "Клон логики $botLogic->name [$time]";
        $clonedBotLogic->description = $botLogic->description;

        DB::beginTransaction();

        $clonedBotLogic->save();

        $this->cloneCommands($botLogic, $clonedBotLogic);
        $this->cloneEvents($botLogic, $clonedBotLogic);
        $this->cloneOperatorNotifications($botLogic, $clonedBotLogic);
        $this->cloneAntispams($botLogic, $clonedBotLogic);
        $this->cloneReminders($botLogic, $clonedBotLogic);
        $this->cloneDistributions($botLogic, $clonedBotLogic);

        DB::commit();

        return $clonedBotLogic;
    }

    private function cloneCommands(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicCommand $command */
        foreach ($originalBotLogic->commands as $command) {
            $clonedCommand = $command->replicate();
            $clonedCommand->bot_logic_id = $clonedBotLogic->id;
            $clonedCommand->save();

            /** @var BotLogicCommandTemplate $template */
            foreach ($command->templates as $template) {
                $clonedTemplate = $template->replicate();
                $clonedTemplate->bot_logic_command_id = $clonedCommand->id;
                $clonedTemplate->save();
            }
        }
    }

    private function cloneEvents(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicEvent $event */
        foreach ($originalBotLogic->events as $event) {
            $cloned = $event->replicate();
            $cloned->bot_logic_id = $clonedBotLogic->id;
            $cloned->save();
        }
    }

    private function cloneOperatorNotifications(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicOperatorNotification $operatorNotification */
        foreach ($originalBotLogic->operatorNotifications as $operatorNotification) {
            $cloned = $operatorNotification->replicate();
            $cloned->bot_logic_id = $clonedBotLogic->id;
            $cloned->save();
        }
    }

    private function cloneAntispams(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicAntispam $antispam */
        foreach ($originalBotLogic->antispams as $antispam) {
            $cloned = $antispam->replicate();
            $cloned->bot_logic_id = $clonedBotLogic->id;
            $cloned->save();
        }
    }

    private function cloneReminders(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicReminder $reminder */
        foreach ($originalBotLogic->reminders as $reminder) {
            $cloned = $reminder->replicate();
            $cloned->bot_logic_id = $clonedBotLogic->id;
            $cloned->save();
        }
    }

    private function cloneDistributions(BotLogic $originalBotLogic, BotLogic $clonedBotLogic): void
    {
        /** @var BotLogicDistribution $distribution */
        foreach ($originalBotLogic->distributions as $distribution) {
            $cloned = $distribution->replicate();
            $cloned->bot_logic_id = $clonedBotLogic->id;
            $cloned->save();
        }
    }
}

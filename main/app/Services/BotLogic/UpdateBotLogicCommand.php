<?php

declare(strict_types=1);

namespace App\Services\BotLogic;

use App\Models\BotLogic\BotLogic;
use App\Models\BotLogic\BotLogicCommand;
use App\Models\BotLogic\BotLogicCommandTemplate;
use App\Models\Seller;
use App\Services\BotLogic\Dto\BotLogicOptionDto;
use App\Services\BotLogic\Dto\UpdateBotLogicDto;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateBotLogicCommand.
 */
final class UpdateBotLogicCommand
{
    public function execute(int $botLogicNumber, Seller $seller, UpdateBotLogicDto $dto): void
    {
        $logic = BotLogic::whereSellerId($seller->id)
            ->whereNumber($botLogicNumber)
            ->with([
                'commands.templates',
                'events',
                'operatorNotifications',
                'antispams',
                'reminders',
                'distributions',
            ])
            ->firstOrFail();

        DB::beginTransaction();

        $logic->name = $dto->getName();
        $logic->description = $dto->getDescription();
        $logic->save();

        $this->updateCommands($dto, $logic);

        $this->updateEvents($dto, $logic);

        $this->updateOperatorNotifications($dto, $logic);

        $this->updateAntispams($dto, $logic);

        $this->updateReminders($dto, $logic);

        $this->updateDistributions($dto, $logic);

        DB::commit();
    }

    private function updateCommands(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getCommands() as $commandDto) {
            /** @var BotLogicCommand $command */
            $command = $logic->commands->where('keys', $commandDto->getKeys())->first();

            foreach ($commandDto->getTemplates() as $templateDto) {
                /** @var BotLogicCommandTemplate $template */
                $template = $command->templates->where('key', $templateDto->getKey())->first();
                $template->content = $templateDto->getContent();
                $template->save();
            }
        }
    }

    private function updateEvents(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getEvents() as $eventDto) {
            $event = $logic->events->where('key', $eventDto->getKey())->first();
            $event->content = $eventDto->getContent();
            $event->save();
        }
    }

    /**
     * @param UpdateBotLogicDto $dto
     * @param BotLogic          $logic
     */
    private function updateOperatorNotifications(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getOperatorNotifications() as $dto) {
            $operatorNotification = $logic->operatorNotifications->where('key', $dto->getKey())->first();
            $operatorNotification->content = $dto->getContent();
            $operatorNotification->save();
        }
    }

    private function updateAntispams(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getAntispams() as $dto) {
            $antispam = $logic->antispams->where('key', $dto->getKey())->first();
            $antispam->options = collect($dto->getOptions())
                ->map(fn (BotLogicOptionDto $optionDto) => $optionDto->toArray())
                ->toArray();
            $antispam->save();
        }
    }

    private function updateReminders(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getReminders() as $dto) {
            $reminder = $logic->reminders->where('key', $dto->getKey())->first();
            $reminder->options = collect($dto->getOptions())
                ->map(fn (BotLogicOptionDto $optionDto) => $optionDto->toArray())
                ->toArray();
            $reminder->save();
        }
    }

    private function updateDistributions(UpdateBotLogicDto $dto, BotLogic $logic): void
    {
        foreach ($dto->getDistributions() as $dto) {
            $distribution = $logic->distributions->where('key', $dto->getKey())->first();
            $distribution->content = $dto->getContent();
            $distribution->save();
        }
    }
}

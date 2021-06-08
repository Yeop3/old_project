<?php

namespace App\Http\Controllers;

use App\Http\Requests\BotLogic\UpdateBotLogicRequest;
use App\Models\BotLogic\BotLogic;
use App\Models\User;
use App\Services\BotLogic\CloneBotLogicCommand;
use App\Services\BotLogic\DeleteBotLogicCommand;
use App\Services\BotLogic\UpdateBotLogicCommand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;

/**
 * Class BotLogicController.
 */
class BotLogicController extends Controller
{
    /**
     * @return Builder[]|Collection
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $with = [
            'commands.templates',
            'events',
            'operatorNotifications',
            'antispams',
            'reminders',
            'distributions',
        ];

        return BotLogic::with($with)
            ->where('seller_id', $user->seller_id)
            ->orWhereNull('seller_id')
            ->get();
    }

    /**
     * @param $type
     * @param $number
     *
     * @return mixed
     */
    public function show($type, $number)
    {
        /** @var User $user */
        $user = auth()->user();

        $logic = BotLogic::with([
            'commands.templates',
            'events',
            'operatorNotifications',
            'antispams',
            'reminders',
            'distributions',
        ])
            ->when($type === 'standard', fn (Builder $builder) => $builder->whereNull('seller_id'))
            ->when($type === 'client', fn (Builder $builder) => $builder->where('seller_id', $user->seller_id))
            ->whereNumber($number)
            ->firstOrFail();

        $config = config('bot_logic.standard');

        $this->fillCommandsInfo($config, $logic);
        $this->fillEventsInfo($config, $logic);
        $this->fillOperatorNotificationsInfo($config, $logic);
        $this->fillAntispamsInfo($config, $logic);
        $this->fillRemindersInfo($config, $logic);
        $this->fillDistributionsInfo($config, $logic);

        return $logic;
    }

    /**
     * @param CloneBotLogicCommand $command
     * @param $type
     * @param $number
     *
     * @return BotLogic
     */
    public function clone(CloneBotLogicCommand $command, $type, $number): BotLogic
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        $logic = BotLogic::with([
            'commands.templates',
            'events',
            'operatorNotifications',
            'antispams',
            'reminders',
            'distributions',
        ])->when($type === 'standard', fn (Builder $builder) => $builder->whereNull('seller_id'))
            ->when($type === 'client', fn (Builder $builder) => $builder->where('seller_id', $user->seller_id))
            ->whereNumber($number)
            ->firstOrFail();

        return $command->execute($logic, $user->seller);
    }

    /**
     * @param UpdateBotLogicRequest $request
     * @param UpdateBotLogicCommand $command
     * @param $number
     */
    public function update(UpdateBotLogicRequest $request, UpdateBotLogicCommand $command, $number): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        $command->execute((int) $number, $user->seller, $request->getDto());
    }

    /**
     * @param DeleteBotLogicCommand $command
     * @param $number
     */
    public function destroy(DeleteBotLogicCommand $command, $number): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === User::ROLE_OPERATOR) {
            abort(403);
        }

        $command->execute((int) $number, $user->seller);
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillCommandsInfo($config, $logic): void
    {
        $commands = collect($config['commands']);

        foreach ($logic->commands as $command) {
            $commandFromConfig = $commands->where('keys', $command->keys)->first();
            $command->title = $commandFromConfig['title'];

            $templates = collect($commandFromConfig['templates']);

            foreach ($command->templates as $template) {
                $templateFromConfig = $templates->where('key', $template->key)->first();
                $template->title = $templateFromConfig['title'];
                $template->description = $templateFromConfig['description'] ?? null;
                $template->tab = $templateFromConfig['tab'] ?? null;
            }
        }
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillEventsInfo($config, $logic): void
    {
        $events = collect($config['events']);

        foreach ($logic->events as $event) {
            $eventFromConfig = $events->where('key', $event->key)->first();
            $event->title = $eventFromConfig['title'];
            $event->description = $eventFromConfig['description'] ?? null;
            $event->tab = $eventFromConfig['tab'] ?? null;
        }
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillOperatorNotificationsInfo($config, $logic): void
    {
        $operatorNotifications = collect($config['operator_notifications']);

        foreach ($logic->operatorNotifications as $operatorNotification) {
            $operatorNotificationFromConfig = $operatorNotifications->where('key', $operatorNotification->key)->first();
            $operatorNotification->title = $operatorNotificationFromConfig['title'];
            $operatorNotification->description = $operatorNotificationFromConfig['description'] ?? null;
            $operatorNotification->tab = $operatorNotificationFromConfig['tab'] ?? null;
        }
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillAntispamsInfo($config, $logic): void
    {
        $antispams = collect($config['antispam']);

        foreach ($logic->antispams as $antispam) {
            $antispamFromConfig = $antispams->where('key', $antispam->key)->first();
            $antispam->title = $antispamFromConfig['title'];

            $options = collect($antispamFromConfig['options']);

            $modelOptions = $antispam->options;

            foreach ($modelOptions as &$option) {
                $optionFromConfig = $options->where('key', $option['key'])->first();
                $option['title'] = $optionFromConfig['title'];
                $option['description'] = $optionFromConfig['description'] ?? null;
                $option['tab'] = $optionFromConfig['tab'] ?? null;
            }

            $antispam->options = $modelOptions;
        }
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillRemindersInfo($config, $logic): void
    {
        $reminders = collect($config['reminders']);

        foreach ($logic->reminders as $reminder) {
            $reminderFromConfig = $reminders->where('key', $reminder->key)->first();
            $reminder->title = $reminderFromConfig['title'];

            $options = collect($reminderFromConfig['options']);

            $modelOptions = $reminder->options;

            foreach ($modelOptions as &$option) {
                $optionFromConfig = $options->where('key', $option['key'])->first();
                $option['title'] = $optionFromConfig['title'];
                $option['description'] = $optionFromConfig['description'] ?? null;
                $option['tab'] = $optionFromConfig['tab'] ?? null;
            }

            $reminder->options = $modelOptions;
        }
    }

    /**
     * @param $config
     * @param $logic
     */
    private function fillDistributionsInfo($config, $logic): void
    {
        $events = collect($config['distribution']);

        foreach ($logic->distributions as $distribution) {
            $distributionFromConfig = $events->where('key', $distribution->key)->first();
            $distribution->title = $distributionFromConfig['title'];
            $distribution->description = $distributionFromConfig['description'] ?? null;
            $distribution->tab = $distributionFromConfig['tab'] ?? null;
        }
    }
}

<?php

namespace App\Http\Requests\BotLogic;

use App\Models\User;
use App\Services\BotLogic\Dto\BotLogicAntispamDto;
use App\Services\BotLogic\Dto\BotLogicCommandDto;
use App\Services\BotLogic\Dto\BotLogicCommandTemplateDto;
use App\Services\BotLogic\Dto\BotLogicDistributionDto;
use App\Services\BotLogic\Dto\BotLogicEventDto;
use App\Services\BotLogic\Dto\BotLogicOperatorNotificationDto;
use App\Services\BotLogic\Dto\BotLogicOptionDto;
use App\Services\BotLogic\Dto\BotLogicReminderDto;
use App\Services\BotLogic\Dto\UpdateBotLogicDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateBotLogicRequest.
 */
class UpdateBotLogicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        $botLogicNumber = $this->route('number');

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('bot_logics')->ignore($botLogicNumber, 'number')->where('seller_id', $user->seller_id),
            ],
            'description' => ['required', 'max:3000'],
        ];
    }

    public function getDto(): UpdateBotLogicDto
    {
        return new UpdateBotLogicDto(
            $this->get('name'),
            $this->get('description'),
            collect($this->get('commands'))
                ->map(fn (array $command) => new BotLogicCommandDto(
                    $command['keys'],
                    collect($command['templates'])
                        ->map(fn (array $template) => new BotLogicCommandTemplateDto(
                            $template['key'],
                            $template['content'],
                        ))
                        ->toArray(),
                ))
                ->toArray(),
            collect($this->get('events'))
                ->map(fn (array $event) => new BotLogicEventDto(
                    $event['key'],
                    $event['content'],
                ))
                ->toArray(),
            collect($this->get('operator_notifications'))
                ->map(fn (array $operatorNotification) => new BotLogicOperatorNotificationDto(
                    $operatorNotification['key'],
                    $operatorNotification['content'],
                ))
                ->toArray(),
            collect($this->get('antispams'))
                ->map(fn (array $antispam) => new BotLogicAntispamDto(
                    $antispam['key'],
                    collect($antispam['options'])
                        ->map(fn (array $option) => new BotLogicOptionDto(
                            $option['key'],
                            $option['value'],
                        ))
                        ->toArray(),
                ))
                ->toArray(),
            collect($this->get('reminders'))
                ->map(fn (array $reminder) => new BotLogicReminderDto(
                    $reminder['key'],
                    collect($reminder['options'])
                        ->map(fn (array $option) => new BotLogicOptionDto(
                            $option['key'],
                            $option['value'],
                        ))
                        ->toArray(),
                ))
                ->toArray(),
            collect($this->get('distributions'))
                ->map(fn (array $distribution) => new BotLogicDistributionDto(
                    $distribution['key'],
                    $distribution['content'],
                ))
                ->toArray(),
        );
    }
}

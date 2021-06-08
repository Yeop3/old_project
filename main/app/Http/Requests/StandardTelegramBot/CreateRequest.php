<?php

namespace App\Http\Requests\StandardTelegramBot;

use App\Models\User;
use App\Services\Bot\Create\CreateStandardTelegramBotDto;
use App\Services\Bot\VO\BotLogicType;
use App\Services\Bot\VO\BotType;
use App\Services\Bot\VO\Messenger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateRequest.
 */
class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('bots')
                    ->where('seller_id', $user->seller_id)
                    ->where('messenger', Messenger::TELEGRAM)
                    ->where('type', BotType::STANDARD)
                    ->where('deleted_at', null),
            ],
            'token'           => ['required', 'max:255'],
            'operator_number' => [
                'nullable',
                'integer',
                Rule::exists('operators', 'number')
                    ->where('seller_id', $user->seller_id)
                    ->whereNotNull('client_id'),
            ],
            'logic_number'         => ['required', 'integer'],
            'logic_type'           => ['required', 'integer', Rule::in(BotLogicType::TYPES)],
            'active'               => ['required', 'boolean'],
            'allow_create_clients' => ['required', 'boolean'],
            'driver_numbers'       => ['nullable', 'array'],
            'driver_numbers.*'     => ['required', 'integer'],
        ];
    }

    public function getDto(): CreateStandardTelegramBotDto
    {
        return new CreateStandardTelegramBotDto(
            $this->get('name'),
            $this->get('token'),
            parseIntFromInput($this->get('logic_number')),
            new BotLogicType(parseIntFromInput($this->get('logic_type'))),
            parseBool($this->get('active')),
            parseBool($this->get('allow_create_clients')),
            $this->get('operator_number'),
            $this->get('driver_numbers', []),
        );
    }
}

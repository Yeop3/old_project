<?php

namespace App\Http\Requests\Wallet\QiwiManual;

use App\Services\Wallet\QiwiManual\Create\CreateQiwiManualWalletDto;
use App\VO\Phone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CreateQiwiManualWalletRequest.
 */
class CreateQiwiManualWalletRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'phone:UA,RU,BY,AZ,IN,GB,GE,LT,TJ,TH,UZ,PA,AM,LV,TR,MD,IL,VN,EE,KP,KG',
                Rule::unique('qiwi_manual_wallets'),
                // TODO сделать проверку по НЕ ручным кошелькам
            ],
            'active'                => ['required', 'boolean'],
            'min_paid_orders_count' => ['required', 'integer', 'min:0'],
            'note'                  => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function getDto(): CreateQiwiManualWalletDto
    {
        return new CreateQiwiManualWalletDto(
            new Phone($this->get('phone')),
            parseBool($this->get('active')),
            parseIntFromInput($this->get('min_paid_orders_count')),
            $this->get('note')
        );
    }
}

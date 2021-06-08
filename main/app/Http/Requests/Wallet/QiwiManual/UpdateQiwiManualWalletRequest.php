<?php

namespace App\Http\Requests\Wallet\QiwiManual;

use App\Services\Wallet\QiwiManual\Update\UpdateQiwiManualWalletDto;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateQiwiManualWalletRequest.
 */
class UpdateQiwiManualWalletRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            'active'                => ['required', 'boolean'],
            'min_paid_orders_count' => ['required', 'integer', 'min:0'],
            'note'                  => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function getDto(): UpdateQiwiManualWalletDto
    {
        return new UpdateQiwiManualWalletDto(
            parseBool($this->get('active')),
            parseIntFromInput($this->get('min_paid_orders_count')),
            $this->get('note')
        );
    }
}

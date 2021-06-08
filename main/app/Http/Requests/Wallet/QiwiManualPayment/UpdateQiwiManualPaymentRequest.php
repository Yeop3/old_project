<?php

namespace App\Http\Requests\Wallet\QiwiManualPayment;

use App\Services\Wallet\QiwiManualPayment\Update\UpdateQiwiManualPaymentDto;
use App\VO\Phone;
use Illuminate\Foundation\Http\FormRequest;
use Money\Currency;
use Money\Money;

/**
 * Class UpdateQiwiManualPaymentRequest.
 */
class UpdateQiwiManualPaymentRequest extends FormRequest
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
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'client_wallet' => ['required', 'phone:UA,RU,BY,AZ,IN,GB,GE,LT,TJ,TH,UZ,PA,AM,LV,TR,MD,IL,VN,EE,KP,KG'],
            'comment'       => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function getDto(): UpdateQiwiManualPaymentDto
    {
        return new UpdateQiwiManualPaymentDto(
            new Money((float) $this->get('amount') * 100, new Currency('UAH')),
            $this->get('client_wallet')
                ? new Phone($this->get('client_wallet'))
                : null,
            $this->get('comment')
        );
    }
}

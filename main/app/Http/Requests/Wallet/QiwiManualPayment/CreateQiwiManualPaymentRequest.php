<?php

namespace App\Http\Requests\Wallet\QiwiManualPayment;

use App\Models\User;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\QiwiManualPayment\Create\CreateQiwiManualPaymentDto;
use App\VO\Phone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Money\Currency;
use Money\Money;

/**
 * Class CreateQiwiManualPaymentRequest.
 */
class CreateQiwiManualPaymentRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->user();

        return [
            'order_number' => [
                'required',
                'integer',
                Rule::exists('orders', 'number')
                    ->where('seller_id', $user->seller_id)
                    ->whereIn('status', [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID]),
            ],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'client_wallet' => ['nullable', 'phone:UA,RU,BY,AZ,IN,GB,GE,LT,TJ,TH,UZ,PA,AM,LV,TR,MD,IL,VN,EE,KP,KG'],
            'comment'       => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function getDto(): CreateQiwiManualPaymentDto
    {
        return new CreateQiwiManualPaymentDto(
            parseIntFromInput($this->get('order_number')),
            new Money((float) $this->get('amount') * 100, new Currency('UAH')),
            $this->get('client_wallet')
                ? new Phone($this->get('client_wallet'))
                : null,
            $this->get('comment')
        );
    }
}

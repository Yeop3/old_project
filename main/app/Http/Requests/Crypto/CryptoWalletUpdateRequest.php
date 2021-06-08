<?php

namespace App\Http\Requests\Crypto;

use App\Models\User;
use App\Services\Wallet\Crypto\Update\CryptoWalletUpdateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CryptoWalletUpdateRequest.
 */
class CryptoWalletUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /* @var User $user */
        $user = auth()->user();

        return [
            'comment'       => ['string', 'nullable'],
            'name'          => ['required', 'string'],
            'proxy_id'      => [
                'nullable',
                'integer',
                Rule::exists('proxies', 'number')
                    ->where('seller_id', $user->seller_id),
            ],
            'confirmations' => [
                'required',
                'integer',
            ],
            //            'payment_type' => [
            //                'required',
            //                'integer',
            //                Rule::in(array_keys(CryptoWalletPaymentType::VALUES))
            //            ],

        ];
    }

    public function getDto(): CryptoWalletUpdateDto
    {
        return new CryptoWalletUpdateDto(
            $this->get('comment'),
            $this->get('name'),
            parseIntFromInput($this->get('proxy_id')),
            $this->get('confirmations')
        );
    }
}

<?php

namespace App\Http\Requests\Crypto;

use App\Models\User;
use App\Services\Wallet\Crypto\Create\CryptoWalletCreateDto;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\VO\CryptoCurrency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CryptoWalletCreateRequest.
 */
class CryptoWalletCreateRequest extends FormRequest
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
            'address'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('crypto_wallets')->where('seller_id', $user->seller_id),
            ],
            'comment'       => ['string', 'nullable'],
            'proxy_id'      => [
                'nullable',
                'integer',
                Rule::exists('proxies', 'number')->where('seller_id', $user->seller_id),
            ],
            'name'          => ['required', 'string'],
            'currency'      => [
                'required',
                'string',
                Rule::in(CryptoCurrency::VALUES),
            ],
            'payment_type'  => [
                'required',
                'integer',
                Rule::in(array_keys(CryptoWalletPaymentType::VALUES)),
            ],
            'confirmations' => [
                'required',
                'integer',
            ],
        ];
    }

    public function getDto(): CryptoWalletCreateDto
    {
        return new CryptoWalletCreateDto(
            $this->get('address'),
            $this->get('comment'),
            $this->get('name'),
            new CryptoCurrency($this->get('currency')),
            new CryptoWalletPaymentType(parseIntFromInput($this->get('payment_type'))),
            parseIntFromInput($this->get('proxy_id')),
            $this->get('confirmations')
        );
    }
}

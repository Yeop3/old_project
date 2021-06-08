<?php

namespace App\Http\Requests\Wallet\EasyPay;

use App\Models\User;
use App\Services\Wallet\EasyPayWallet\Create\EasyPayWalletCreateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class EasyPayCreateRequest.
 */
class EasyPayCreateRequest extends FormRequest
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
            'wallet_number' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'password'      => ['required', 'string', 'max:255'],
            'proxy_number'  => [
                'required',
                'integer',
                Rule::exists('proxies', 'number')->where('seller_id', $user->seller_id),
            ],
            'limit'         => [
                'required',
                'numeric',
            ],
        ];
    }

    public function getDto(): EasyPayWalletCreateDto
    {
        return new EasyPayWalletCreateDto(
            $this->get('phone'),
            $this->get('name'),
            $this->get('password'),
            $this->get('proxy_number'),
            $this->get('wallet_number'),
            (float) $this->get('limit')
        );
    }
}

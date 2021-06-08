<?php

namespace App\Http\Requests\Wallet\GlobalMoney;

use App\Models\User;
use App\Services\Wallet\GlobalMoney\Create\GlobalMoneyCreateDto;
use App\Services\Wallet\GlobalMoney\VO\GlobalMoneyLoginData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class GlobalMoneyWalletCreateRequest.
 */
class GlobalMoneyWalletCreateRequest extends FormRequest
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
            'login'         => ['required', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'password'      => ['required', 'string', 'max:255'],
            'proxy_number'  => [
                'nullable',
                'integer',
                Rule::exists('proxies', 'number')->where('seller_id', $user->seller_id),
            ],
            'type'   => ['required', 'string', Rule::in(GlobalMoneyLoginData::LOGIN_TYPES)],
            'active' => ['required', 'boolean'],
        ];
    }

    public function getDto(): GlobalMoneyCreateDto
    {
        return new GlobalMoneyCreateDto(
            $this->get('wallet_number'),
            $this->get('login'),
            $this->get('name'),
            $this->get('password'),
            $this->get('type'),
            parseBool($this->get('active')),
            parseIntFromInput($this->get('proxy_number')),
        );
    }
}

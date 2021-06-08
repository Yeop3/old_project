<?php

namespace App\Http\Requests\Wallet\Kuna;

use App\Models\User;
use App\Services\Wallet\Kuna\Create\KunaWalletCreateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class KunaWalletCreateRequest.
 */
class KunaWalletCreateRequest extends FormRequest
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
            'name'        => ['required', 'string'],
            'comment'     => ['string', 'nullable'],
            'active'      => ['required', 'boolean'],
            'public_key'  => ['required', 'string'],
            'private_key' => ['required', 'string'],
            'proxy_id'    => [
                'nullable',
                'integer',
                Rule::exists('proxies', 'number')->where('seller_id', $user->seller_id),
            ],
        ];
    }

    public function getDto(): KunaWalletCreateDto
    {
        return new KunaWalletCreateDto(
            $this->get('name'),
            $this->get('comment'),
            parseBool($this->get('active')),
            $this->get('public_key'),
            $this->get('private_key'),
            parseIntFromInput($this->get('proxy_id')),
        );
    }
}

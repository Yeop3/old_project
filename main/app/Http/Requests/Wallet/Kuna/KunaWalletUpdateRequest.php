<?php

namespace App\Http\Requests\Wallet\Kuna;

use App\Models\User;
use App\Services\Wallet\Kuna\Update\KunaWalletUpdateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class KunaWalletUpdateRequest.
 */
class KunaWalletUpdateRequest extends FormRequest
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
            'name'        => ['string'],
            'active'      => ['required', 'boolean'],
            'comment'     => ['string', 'nullable'],
            'public_key'  => ['required', 'string'],
            'private_key' => ['required', 'string'],
            'proxy_id'    => [
                'nullable',
                'integer',
                Rule::exists('proxies', 'number')
                    ->where('seller_id', $user->seller_id),
            ],
        ];
    }

    public function getDto(): KunaWalletUpdateDto
    {
        return new KunaWalletUpdateDto(
            $this->get('name'),
            $this->get('comment'),
            parseBool($this->get('active')),
            $this->get('public_key'),
            $this->get('private_key'),
            parseIntFromInput($this->get('proxy_id')),
        );
    }
}

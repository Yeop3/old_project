<?php

namespace App\Http\Requests\Proxy;

use App\Services\Proxy\Create\ProxyDto;
use App\Services\Proxy\VO\ProxyType;
use App\VO\Ip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateProxyRequest.
 */
class UpdateProxyRequest extends FormRequest
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
            'proxy_type' => ['required', 'string', Rule::in(ProxyType::VALUES)],
            'ip'         => ['required', 'string', 'ip'],
            'port'       => ['required', 'integer', 'min:0', 'max:65535'],
            'username'   => ['required_with:password', 'string', 'max:255'],
            'password'   => ['required_with:username', 'string', 'max:255'],
        ];
    }

    public function getDto(): ProxyDto
    {
        return new ProxyDto(
            new Ip($this->get('ip')),
            parseIntFromInput($this->get('port')),
            new ProxyType($this->get('proxy_type')),
            $this->get('username'),
            $this->get('password'),
            $this->get('note'),
        );
    }
}

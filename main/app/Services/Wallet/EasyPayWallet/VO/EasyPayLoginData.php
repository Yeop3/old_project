<?php

declare(strict_types=1);

namespace App\Services\Wallet\EasyPayWallet\VO;

use JsonSerializable;

/**
 * Class EasyPayLoginData.
 */
final class EasyPayLoginData implements JsonSerializable
{
    private string $phone;
    private string $password;

    public function __construct(string $phone, string $password)
    {
        $this->phone = $phone;
        $this->password = $password;
    }

    public function toArrayForApi(): array
    {
        return [
            'username' => trim($this->phone, '+'),
            'password' => $this->password,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'phone'    => $this->phone,
            'password' => $this->password,
        ];
    }
}

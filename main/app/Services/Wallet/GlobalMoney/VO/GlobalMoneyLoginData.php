<?php

declare(strict_types=1);

namespace App\Services\Wallet\GlobalMoney\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class GlobalMoneyLoginData.
 */
final class GlobalMoneyLoginData implements JsonSerializable
{
    public const LOGIN_TYPE_PHONE = 'phone';
    public const LOGIN_TYPE_TEXT = 'text';

    public const LOGIN_TYPES = [
        self::LOGIN_TYPE_PHONE,
        self::LOGIN_TYPE_TEXT,
    ];
    private string $login;
    private string $password;
    private string $type;

    public function __construct(string $login, string $password, string $type)
    {
        Assert::true(in_array($type, self::LOGIN_TYPES, true), 'Wrong type');

        $this->login = $login;
        $this->password = $password;
        $this->type = $type;
    }

    public function toArrayForApi(): array
    {
        return [
            'login' => $this->type === 'phone'
                ? preg_replace('#^\+#ui', '', $this->login)
                : $this->login,
            'password'  => $this->password,
            'loginType' => $this->type,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'login'    => $this->login,
            'password' => $this->password,
            'type'     => $this->type,
        ];
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

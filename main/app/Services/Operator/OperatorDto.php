<?php

declare(strict_types=1);

namespace App\Services\Operator;

/**
 * Class OperatorDto.
 */
final class OperatorDto
{
    private string $name;

    private ?string $password;

    private ?string $email;

    private int $clientNumber;

    public function __construct(string $name, int $clientNumber, ?string $email, ?string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->clientNumber = $clientNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): ?string
    {
        if (!$this->password) {
            return null;
        }

        return bcrypt($this->password);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getClientNumber(): int
    {
        return $this->clientNumber;
    }
}

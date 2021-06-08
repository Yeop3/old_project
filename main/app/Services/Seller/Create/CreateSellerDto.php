<?php

declare(strict_types=1);

namespace App\Services\Seller\Create;

/**
 * Class CreateSellerDto.
 */
final class CreateSellerDto
{
    private string $name;
    private string $domain;
    private string $userPassword;

    public function __construct(string $name, string $domain, string $userPassword)
    {
        $this->name = $name;
        $this->domain = $domain;
        $this->userPassword = $userPassword;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getUserPassword(): string
    {
        return $this->userPassword;
    }
}

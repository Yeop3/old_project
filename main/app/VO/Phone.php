<?php

declare(strict_types=1);

namespace App\VO;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * Class Phone.
 */
final class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phone = $phoneUtil->parse($value);
        } catch (NumberParseException $e) {
            $this->throwInvalid($value);
        }

        if (!$phoneUtil->isValidNumber($phone)) {
            $this->throwInvalid($value);
        }

        $this->value = $value;
    }

    private function throwInvalid(string $value): void
    {
        throw new InvalidArgumentException('Invalid phone '.$value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

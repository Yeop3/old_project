<?php

declare(strict_types=1);

namespace App\VO;

use Stringable;
use Webmozart\Assert\Assert;

/**
 * Class Ip.
 */
final class Ip implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::ip($value, 'Wrong type of ip');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}

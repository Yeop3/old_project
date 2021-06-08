<?php

declare(strict_types=1);

namespace App\Services\Order\Exceptions;

use App\Exceptions\BusinessException;

/**
 * Class WrongOrderStatusForPaymentException.
 */
final class WrongOrderStatusForPaymentException extends BusinessException
{
}

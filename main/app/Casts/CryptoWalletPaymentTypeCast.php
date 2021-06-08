<?php

declare(strict_types=1);

namespace App\Casts;

use App\Services\Wallet\VO\CryptoWalletPaymentType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CryptoWalletPaymentTypeCast.
 */
final class CryptoWalletPaymentTypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param int    $value
     * @param array  $attributes
     *
     * @return CryptoWalletPaymentType
     */
    public function get($model, string $key, $value, array $attributes): CryptoWalletPaymentType
    {
        return new CryptoWalletPaymentType((int) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model                   $model
     * @param string                  $key
     * @param CryptoWalletPaymentType $value
     * @param array                   $attributes
     *
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return $value->getValue();
    }
}

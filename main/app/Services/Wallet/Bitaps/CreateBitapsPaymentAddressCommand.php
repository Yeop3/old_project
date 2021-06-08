<?php

declare(strict_types=1);

namespace App\Services\Wallet\Bitaps;

use App\Models\Proxy;
use App\VO\CryptoCurrency;

/**
 * Class CreateBitapsPaymentAddressCommand.
 */
final class CreateBitapsPaymentAddressCommand
{
    /**
     * @param CryptoCurrency                $currency
     * @param CreateBitapsPaymentAddressDto $dto
     * @param Proxy|null                    $proxy
     *
     * @return CreateForwardingAddressResponse
     */
    public function execute(CryptoCurrency $currency, CreateBitapsPaymentAddressDto $dto, ?Proxy $proxy = null): CreateForwardingAddressResponse
    {
        $bitapsPaymentForwardingApi = new BitapsPaymentForwardingApi($currency);

        $result = $bitapsPaymentForwardingApi->createForwardingAddress(
            $dto->getForwardingAddress(),
            $dto->getCallbackLink(),
            $dto->getConfirmations(),
            $proxy
        );

        return new CreateForwardingAddressResponse(
            $result['payment_code'],
            $result['callback_link'] ?? null,
            $result['forwarding_address'],
            $result['domain_hash'] ?? null,
            $result['confirmations'],
            $result['address'],
            $result['legacy_address'] ?? null,
            $result['domain'] ?? null,
            $result['invoice'],
            $result['currency'],
        );
    }
}

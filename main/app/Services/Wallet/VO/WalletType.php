<?php

declare(strict_types=1);

namespace App\Services\Wallet\VO;

use App\Models\Wallet\CryptoWallet;
use App\Models\Wallet\EasyPayWallet;
use App\Models\Wallet\GlobalMoneyWallet;
use App\Models\Wallet\KunaAccount;
use App\Models\Wallet\QiwiManualWallet;
use InvalidArgumentException;
use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class WalletType.
 */
final class WalletType implements JsonSerializable
{
    public const TYPE_QIWI_MANUAL = 1;
    public const TYPE_CRYPTO_BTC = 2;
    public const TYPE_CRYPTO_BCH = 3;
    public const TYPE_CRYPTO_LTC = 4;
    public const TYPE_CRYPTO_ETH = 5;
    public const TYPE_KUNA_CODE = 6;
    public const TYPE_GLOBAL_MONEY = 7;
    public const TYPE_EASY_PAY = 8;

    public const TYPES = [
        self::TYPE_QIWI_MANUAL  => 'QIWI ручной',
        self::TYPE_CRYPTO_BTC   => 'BTC',
        self::TYPE_CRYPTO_BCH   => 'BCH',
        self::TYPE_CRYPTO_LTC   => 'LTC',
        self::TYPE_CRYPTO_ETH   => 'ETH',
        self::TYPE_KUNA_CODE    => 'KUNA код(UAH)',
        self::TYPE_GLOBAL_MONEY => 'Global money',
        self::TYPE_EASY_PAY     => 'Easypay',
    ];

    public const PAYMENT_METHODS = [
        self::TYPE_QIWI_MANUAL => [
            PaymentMethod::QIWI_MANUAL_UAH,
        ],
        self::TYPE_CRYPTO_BTC => [
            PaymentMethod::CRYPTO_BTC,
        ],
        self::TYPE_CRYPTO_BCH => [
            PaymentMethod::CRYPTO_BCH,
        ],
        self::TYPE_CRYPTO_LTC => [
            PaymentMethod::CRYPTO_LTC,
        ],
        self::TYPE_CRYPTO_ETH => [
            PaymentMethod::CRYPTO_ETH,
        ],
        self::TYPE_KUNA_CODE => [
            PaymentMethod::KUNA_CODE_UAH,
        ],
        self::TYPE_GLOBAL_MONEY => [
            //            PaymentMethod::GLOBAL_MONEY_CARD,
            PaymentMethod::GLOBAL_MONEY_ONLINE,
            PaymentMethod::GLOBAL_MONEY_TERMINAL,
        ],
        self::TYPE_EASY_PAY => [
            PaymentMethod::EASY_PAY_ONLINE,
            PaymentMethod::EASY_PAY_TERMINAL,
        ],
    ];

    public const MODELS_MORPH_MAP = [
        self::TYPE_QIWI_MANUAL  => 'wallet_qiwi_manual',
        self::TYPE_CRYPTO_BTC   => 'wallet_crypto',
        self::TYPE_CRYPTO_BCH   => 'wallet_crypto',
        self::TYPE_CRYPTO_LTC   => 'wallet_crypto',
        self::TYPE_CRYPTO_ETH   => 'wallet_crypto',
        self::TYPE_KUNA_CODE    => 'kuna_account',
        self::TYPE_GLOBAL_MONEY => 'global_money',
        self::TYPE_EASY_PAY     => 'easy_pay',
    ];

    public const TRANSACTION_RELATION_NAME = [
        self::TYPE_QIWI_MANUAL  => 'payments',
        self::TYPE_CRYPTO_BTC   => 'cryptoTransactions',
        self::TYPE_CRYPTO_BCH   => 'cryptoTransactions',
        self::TYPE_CRYPTO_LTC   => 'cryptoTransactions',
        self::TYPE_CRYPTO_ETH   => 'cryptoTransactions',
        self::TYPE_KUNA_CODE    => 'kunaCodes',
        self::TYPE_GLOBAL_MONEY => 'transactions',
        self::TYPE_EASY_PAY     => 'transactions',
    ];

    public const MODELS_MAP = [
        self::TYPE_QIWI_MANUAL  => QiwiManualWallet::class,
        self::TYPE_CRYPTO_BTC   => CryptoWallet::class,
        self::TYPE_CRYPTO_BCH   => CryptoWallet::class,
        self::TYPE_CRYPTO_LTC   => CryptoWallet::class,
        self::TYPE_CRYPTO_ETH   => CryptoWallet::class,
        self::TYPE_KUNA_CODE    => KunaAccount::class,
        self::TYPE_GLOBAL_MONEY => GlobalMoneyWallet::class,
        self::TYPE_EASY_PAY     => EasyPayWallet::class,
    ];

    private int $value;

    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong wallet type');

        $this->value = $value;
    }

    public static function createByPaymentMethod(PaymentMethod $paymentMethod): self
    {
        foreach (self::PAYMENT_METHODS as $selfValue => $paymentMethodValues) {
            foreach ($paymentMethodValues as $paymentMethodValue) {
                if ($paymentMethod->getValue() === $paymentMethodValue) {
                    return new self($selfValue);
                }
            }
        }

        throw new InvalidArgumentException('Wrong PaymentMethod object');
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getReadableValue(): string
    {
        return self::TYPES[$this->value];
    }

    /**
     * @return PaymentMethod[]
     */
    public function getPaymentMethods(): array
    {
        return array_map(
            fn (int $paymentMethod) => new PaymentMethod($paymentMethod),
            self::PAYMENT_METHODS[$this->value]
        );
    }

    public function getModelClass(): string
    {
        return self::MODELS_MAP[$this->value];
    }

    public function getModelMorphClass(): string
    {
        return self::MODELS_MORPH_MAP[$this->value];
    }

    public function getTransactionRelationName(): string
    {
        return self::TRANSACTION_RELATION_NAME[$this->value];
    }

    /**
     * @return int|mixed
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }

    public static function getModelsMorphArray(): array
    {
        $maps = [];

        foreach (self::MODELS_MAP as $key => $item) {
            $maps[(new WalletType($key))->getModelMorphClass()] = (new WalletType($key))->getModelClass();
        }

        return $maps;
    }
}

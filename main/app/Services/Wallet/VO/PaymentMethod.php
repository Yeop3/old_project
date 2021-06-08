<?php

declare(strict_types=1);

namespace App\Services\Wallet\VO;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class PaymentMethod.
 */
final class PaymentMethod implements JsonSerializable
{
    public const QIWI_MANUAL_UAH = 1;
    public const CRYPTO_BTC = 2;
    public const CRYPTO_BCH = 3;
    public const CRYPTO_LTC = 4;
    public const CRYPTO_ETH = 5;
    public const KUNA_CODE_UAH = 6;
    public const GLOBAL_MONEY_CARD = 7;
    public const GLOBAL_MONEY_ONLINE = 8;
    public const GLOBAL_MONEY_TERMINAL = 9;
    public const EASY_PAY_ONLINE = 10;
    public const EASY_PAY_TERMINAL = 11;

    public const TYPES = [
        self::QIWI_MANUAL_UAH       => 'QIWI ручной',
        self::CRYPTO_BTC            => 'BTC',
        self::CRYPTO_BCH            => 'BCH',
        self::CRYPTO_LTC            => 'LTC',
        self::CRYPTO_ETH            => 'ETH',
        self::KUNA_CODE_UAH         => 'KUNA код(UAH)',
        self::GLOBAL_MONEY_CARD     => 'Visa/Mastercard (Global money)',
        self::GLOBAL_MONEY_ONLINE   => 'Global money (online)',
        self::GLOBAL_MONEY_TERMINAL => 'Global money (terminal)',
        self::EASY_PAY_ONLINE       => 'Easypay (online)',
        self::EASY_PAY_TERMINAL     => 'Easypay (terminal)',
    ];

    public const CURRENCIES = [
        self::QIWI_MANUAL_UAH       => 'uah',
        self::CRYPTO_BTC            => 'btc',
        self::CRYPTO_BCH            => 'bch',
        self::CRYPTO_LTC            => 'ltc',
        self::CRYPTO_ETH            => 'eth',
        self::KUNA_CODE_UAH         => 'uah',
        self::GLOBAL_MONEY_CARD     => 'uah',
        self::GLOBAL_MONEY_ONLINE   => 'uah',
        self::GLOBAL_MONEY_TERMINAL => 'uah',
        self::EASY_PAY_ONLINE       => 'uah',
        self::EASY_PAY_TERMINAL     => 'uah',
    ];

    public const TEMPLATE_KEYS = [
        'order_created' => [
            self::QIWI_MANUAL_UAH       => 'order_qiwi_3',
            self::CRYPTO_BTC            => 'order_crypto_btc',
            self::CRYPTO_BCH            => 'order_crypto_bch',
            self::CRYPTO_LTC            => 'order_crypto_ltc',
            self::CRYPTO_ETH            => 'order_crypto_eth',
            self::KUNA_CODE_UAH         => 'order_kuna',
            self::GLOBAL_MONEY_CARD     => 'order_global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'order_global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'order_global_money_terminal',
            self::EASY_PAY_ONLINE       => 'order_easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'order_easy_pay_terminal',
        ],
        'order_wait_payment' => [
            self::QIWI_MANUAL_UAH       => 'order_wait_payment_qiwi_3',
            self::CRYPTO_BTC            => 'order_wait_payment_crypto_btc',
            self::CRYPTO_BCH            => 'order_wait_payment_crypto_bch',
            self::CRYPTO_LTC            => 'order_wait_payment_crypto_ltc',
            self::CRYPTO_ETH            => 'order_wait_payment_crypto_eth',
            self::KUNA_CODE_UAH         => 'order_wait_payment_kuna',
            self::GLOBAL_MONEY_CARD     => 'order_wait_payment_global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'order_wait_payment_global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'order_wait_payment_global_money_terminal',
            self::EASY_PAY_ONLINE       => 'order_wait_payment_easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'order_wait_payment_easy_pay_terminal',
        ],
        'order_partially_paid' => [
            self::QIWI_MANUAL_UAH       => 'order_pay_partially_qiwi_3',
            self::CRYPTO_BTC            => 'order_pay_partially_crypto_btc',
            self::CRYPTO_BCH            => 'order_pay_partially_crypto_bch',
            self::CRYPTO_LTC            => 'order_pay_partially_crypto_ltc',
            self::CRYPTO_ETH            => 'order_pay_partially_crypto_eth',
            self::KUNA_CODE_UAH         => 'order_pay_partially_kuna',
            self::GLOBAL_MONEY_CARD     => 'order_pay_partially_global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'order_pay_partially_global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'order_pay_partially_global_money_terminal',
            self::EASY_PAY_ONLINE       => 'order_pay_partially_easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'order_pay_partially_easy_pay_terminal',
        ],
    ];

    public const EVENT_KEYS = [
        'partially_paid' => [
            self::QIWI_MANUAL_UAH       => 'order_partially_paid_qiwi_3',
            self::CRYPTO_BTC            => 'order_partially_paid_crypto_btc',
            self::CRYPTO_BCH            => 'order_partially_paid_crypto_bch',
            self::CRYPTO_LTC            => 'order_partially_paid_crypto_ltc',
            self::CRYPTO_ETH            => 'order_partially_paid_crypto_eth',
            self::KUNA_CODE_UAH         => 'order_partially_paid_kuna',
            self::GLOBAL_MONEY_CARD     => 'order_partially_paid_global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'order_partially_paid_global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'order_partially_paid_global_money_terminal',
            self::EASY_PAY_ONLINE       => 'order_partially_paid_easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'order_partially_paid_easy_pay_terminal',
        ],
    ];

    public const REMINDER_KEYS = [
        'partially_paid' => [
            self::QIWI_MANUAL_UAH       => 'qiwi_3',
            self::CRYPTO_BTC            => 'crypto_btc',
            self::CRYPTO_BCH            => 'crypto_bch',
            self::CRYPTO_LTC            => 'crypto_ltc',
            self::CRYPTO_ETH            => 'crypto_eth',
            self::KUNA_CODE_UAH         => 'kuna',
            self::GLOBAL_MONEY_CARD     => 'global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'global_money_terminal',
            self::EASY_PAY_ONLINE       => 'easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'easy_pay_terminal',
        ],
        'payment' => [
            self::QIWI_MANUAL_UAH       => 'qiwi_3',
            self::CRYPTO_BTC            => 'crypto_btc',
            self::CRYPTO_BCH            => 'crypto_bch',
            self::CRYPTO_LTC            => 'crypto_ltc',
            self::CRYPTO_ETH            => 'crypto_eth',
            self::KUNA_CODE_UAH         => 'kuna',
            self::GLOBAL_MONEY_CARD     => 'global_money_card',
            self::GLOBAL_MONEY_ONLINE   => 'global_money_online',
            self::GLOBAL_MONEY_TERMINAL => 'global_money_terminal',
            self::EASY_PAY_ONLINE       => 'easy_pay_online',
            self::EASY_PAY_TERMINAL     => 'easy_pay_terminal',
        ],
    ];

    public const RESERVATION_TIME_KEY = [
        self::QIWI_MANUAL_UAH       => 'reservation_time_qiwi_manual',
        self::CRYPTO_BTC            => 'reservation_time_crypto',
        self::CRYPTO_BCH            => 'reservation_time_crypto',
        self::CRYPTO_LTC            => 'reservation_time_crypto',
        self::CRYPTO_ETH            => 'reservation_time_crypto',
        self::KUNA_CODE_UAH         => 'reservation_time_kuna',
        self::GLOBAL_MONEY_CARD     => 'reservation_time_global_money_card',
        self::GLOBAL_MONEY_ONLINE   => 'reservation_time_global_money_online',
        self::GLOBAL_MONEY_TERMINAL => 'reservation_time_global_money_terminal',

        self::EASY_PAY_ONLINE   => 'reservation_time_easy_pay_online',
        self::EASY_PAY_TERMINAL => 'reservation_time_easy_pay_terminal',
    ];

    private int $value;

    /**
     * PaymentMethod constructor.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $values = array_keys(self::TYPES);

        Assert::true(in_array($value, $values, true), 'Wrong payment method');

        $this->value = $value;
    }

    /**
     * @return array
     */
    public static function getInitTypesForProductTypes(): array
    {
        return array_keys(self::TYPES);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getReadableValue(): string
    {
        return self::TYPES[$this->value];
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return self::CURRENCIES[$this->value];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getTemplateKey(string $key): string
    {
        return self::TEMPLATE_KEYS[$key][$this->value];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getEventKey(string $key): string
    {
        return self::EVENT_KEYS[$key][$this->value];
    }

    /**
     * @return string
     */
    public function getReservationTimeKey(): string
    {
        return self::RESERVATION_TIME_KEY[$this->value];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getReminderKey(string $key): string
    {
        return self::REMINDER_KEYS[$key][$this->value];
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

use App\Services\Wallet\VO\CryptoWalletResolvingType;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\QiwiTransactionIdentification;
use App\VO\CommissionType;
use Illuminate\Validation\Rule;

return [
    'common' => [
        'monitoring_system_on' => [
            'value' => 0,
            'rules' => 'boolean',
        ],

        'monitoring_system_latency' => [
            'value' => 30,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'admin_panel_name' => [
            'value' => 'Админка',
            'rules' => ['required', 'string', 'max:255'],
        ],

        'count_product_for_notify' => [
            'value' => 5,
            'rules' => ['required', 'integer', 'min:1'],
        ],
    ],

    'reservation' => [
        'reservation_type' => [
            'value' => 'old',
            'rules' => ['required', 'string', 'in:old,random'],
        ],

        'auto_cancel_partially_paid_orders' => [
            'value' => 0,
            'rules' => ['required', 'boolean'],
        ],

        'reservation_time_qiwi_manual' => [
            'value' => 120,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_crypto' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_kuna' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_global_money_card' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_global_money_online' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_global_money_terminal' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_easy_pay_online' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'reservation_time_easy_pay_terminal' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],
    ],

    'anticaptcha' => [
        'key' => [
            'value' => null,
            'rules' => ['nullable', 'string', 'max:255'],
        ],
    ],

    'qiwi' => [
        'name_in_bot' => [
            'value' => 'QIWI',
            'rules' => ['required', 'string', 'max:255'],
        ],

        'commission_value' => [
            'value' => 0,
            'rules' => ['required', 'integer', 'min:0'],
        ],

        'commission_type' => [
            'value' => 1,
            'rules' => ['required', 'integer',
                // Rule::in(array_keys(CommissionType::TYPES))
                'in:'.implode(',', array_keys(CommissionType::TYPES)),
            ],
        ],

        'useragent' => [
            'value' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
            'rules' => ['required', 'string', 'max:255'],
        ],

        'deactivate_on_ban_suspicion' => [
            'value' => 1,
            'rules' => ['boolean'],
        ],

        'cookies_clean_interval' => [
            'value' => 60,
            'rules' => ['required', 'integer', 'min:1'],
        ],

        'clear_soft_deleted_wallets_interval' => [
            'value' => 30,
            'rules' => ['required', 'integer', 'min:1'],
        ],

//        'transaction_identification_method' => [
//            'value' => 1,
//            'rules' => ['required', 'integer', Rule::in(array_keys(QiwiTransactionIdentification::VALUES))],
//        ],
    ],

    'crypto'=> [
        'wallets_resolving_type' => [
            'value' => CryptoWalletResolvingType::ROTATE_AND_BITAPS,
            'rules' => ['required', 'string',
                // Rule::in(array_keys(CryptoWalletResolvingType::VALUES))
                'in:'.implode(',', array_keys(CryptoWalletResolvingType::VALUES)),
            ],
        ],
//        'name_btc_payment_in_bot' =>[
//            'value' => 'Bitcoin (BTC)',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'commission_btc' => [
//            'value' => 0,
//            'rules' => ['required', 'integer']
//        ],
//        'commission_type_btc' => [
//            'value' => 'rub',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'name_bch_payment_in_bot' => [
//            'value' => 'Bitcoin Cash (BCH)',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'commission_bch' => [
//            'value' => 0,
//            'rules' => ['required', 'integer']
//        ],
//        'commission_type_bch' => [
//            'value' => 'rub',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'name_ltc_payment_in_bot' => [
//            'value' => 'Litecoin (LTC)',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'commission_ltc' => [
//            'value' => 0,
//            'rules' => ['required', 'integer']
//        ],
//        'commission_type_ltc' => [
//            'value' => 'rub',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'name_eth_payment_in_bot' => [
//            'value' => 'Ethereum (ETH)',
//            'rules' => ['required', 'string', 'max:255']
//        ],
//        'commission_eth' => [
//            'value' => 0,
//            'rules' => ['required', 'integer']
//        ],
//        'commission_type_eth' => [
//            'value' => 'rub',
//            'rules' => ['required', 'string', 'max:255']
//        ],
    ],

    'payment_method_enable_toggle' => [
        PaymentMethod::TYPES[PaymentMethod::QIWI_MANUAL_UAH] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::CRYPTO_BTC] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::CRYPTO_BCH] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::CRYPTO_LTC] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::CRYPTO_ETH] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::KUNA_CODE_UAH] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::GLOBAL_MONEY_CARD] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::GLOBAL_MONEY_ONLINE] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::GLOBAL_MONEY_TERMINAL] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::EASY_PAY_ONLINE] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
        PaymentMethod::TYPES[PaymentMethod::EASY_PAY_TERMINAL] => [
            'value' => 1,
            'rules' => ['required', 'boolean'],
        ],
    ],

];

<?php

use App\Models\Order;
use App\Services\Currency\CryptoCurrencies;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * @param $value
 *
 * @return bool
 */
function parseBool($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_string($value)) {
        return $value === '1' || $value === 'true';
    }

    return $value === 1;
}

/**
 * @param int $limit
 *
 * @return int
 */
function getRightApiLimit(int $limit): int
{
    if ($limit > 100 || $limit < 1) {
        return 20;
    }

    return $limit;
}

/**
 * @param string $url
 *
 * @return string
 */
function getFileFullUrl(string $url): string
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $url = Storage::url($url);
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $url = config('app.url').$url;
    }

    return $url;
}

/**
 * @param string|null $input
 *
 * @return int|null
 */
function parseIntFromInput(?string $input): ?int
{
    return $input !== null
        ? (int) $input
        : null;
}

/**
 * @param string|null $input
 *
 * @return float|null
 */
function parseFloatFromInput(?string $input): ?float
{
    return $input !== null
        ? (float) $input
        : null;
}

/**
 * @param array $array
 * @param array $clearKeys
 *
 * @return array
 */
function clearArray(array $array, array $clearKeys): array
{
    $array = \Illuminate\Support\Arr::except($array, $clearKeys);

    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = clearArray($v, $clearKeys);
        }
    }

    return $array;
}

/**
 * @param string|null $date
 *
 * @return Carbon|null
 */
function carbonSafeParse(?string $date = null): ?Carbon
{
    if (!$date) {
        return null;
    }

    try {
        return Carbon::parse($date);
    } catch (InvalidFormatException $e) {
        return null;
    }
}

/**
 * @param Money           $money
 * @param Currencies|null $currencies
 *
 * @return string
 */
function formatMoney(Money $money, ?Currencies $currencies = null): string
{
    $currencies = $currencies ?? new ISOCurrencies();

    $moneyFormatter = new DecimalMoneyFormatter($currencies);

    return $moneyFormatter->format($money);
}

function convertToCryptoFromRub(Money $money, Currency $currency, ?float $rate = null): Money
{
    if (!$rate) {
        $rates = Cache::get('crypto_price');
        $rate = $rates["{$currency->getCode()}_UAH"]['buy_price'];
    }

    $amountReal = bcdiv($money->getAmount(), 100, 2);

    $value = bcdiv($amountReal, $rate, 8);

    $cryptoValue = bcmul($value, 100000000, 0);

    return new Money($cryptoValue, $currency);
}

function convertToRubFromCrypto(Money $money, Currency $currency, ?float $rate = null): Money
{
    if (!$rate) {
        $rates = Cache::get('crypto_price');
        $rate = $rates["{$currency->getCode()}_UAH"]['buy_price'];
    }

    $amountReal = bcdiv($money->getAmount(), 100000000, 8);

    $value = bcmul($amountReal, $rate, 8);
    $value = round($value, 2);

    $uahValue = bcmul($value, 100, 2);

    return new Money($uahValue, new Currency('UAH'));
}

function getOrderCryptoUnpaidText(Order $order): string
{
    if (!$order->wallet->isCrypto()) {
        return '';
    }

    return formatMoney(
        $order->getCryptoUnpaidAmount(),
        new CryptoCurrencies()
    )
        .' '
        .$order->wallet->getCurrency()->getCode();
}

/**
 * @param Order      $order
 * @param Money|null $paidAmount
 *
 * @return string
 */
function getOrderCryptoPaidText(Order $order, ?Money $paidAmount = null): string
{
    if (!$order->wallet->isCrypto()) {
        return '';
    }

    if (!$paidAmount) {
        $paidAmount = $order->getPaidAmount();
    }

    return formatMoney(
        $order->getCryptoPaidAmount($paidAmount),
        new CryptoCurrencies()
    )
        .' '
        .$order->wallet->getCurrency()->getCode();
}

/**
 * @param $value
 * @param $rules
 * @param string $key
 *
 * @return bool
 */
function isValidValue($value, $rules, $key = null): bool
{
    $key = $key ?? 'value';

    return !(Validator::make([$key => $value], [$key => $rules])->fails());
}

/**
 * @param $value
 * @param $rules
 * @param string $key
 *
 * @return \Illuminate\Contracts\Validation\Validator
 */
function valueValidator($value, $rules, $key = null): \Illuminate\Contracts\Validation\Validator
{
    $key = $key ?? 'value';

    return Validator::make([$key => $value], [$key => $rules]);
}

/**
 * @param \Illuminate\Contracts\Validation\Validator $validator
 *
 * @return string
 */
function valueValidatorError(\Illuminate\Contracts\Validation\Validator $validator): string
{
    return $validator->errors()->first() ?? 'Вы ввели неверные данные, попробуйте еще раз.';
}

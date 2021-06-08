<?php

namespace App\Models\Wallet;

use App\Casts\CryptoCurrencyCast;
use App\Casts\CryptoWalletPaymentTypeCast;
use App\Models\Order;
use App\Models\Proxy;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\Shift;
use App\Models\User;
use App\Services\Order\GetCryptoPricingCommand;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\Bitaps\CreateBitapsPaymentAddressCommand;
use App\Services\Wallet\Bitaps\CreateBitapsPaymentAddressDto;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\Services\Wallet\VO\WalletType;
use App\VO\CryptoCurrency;
use Eloquent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Money\Currency;
use Money\Money;
use RuntimeException;
use Throwable;

/**
 * App\Models\Wallet\CryptoWallet.
 *
 * @property int                     $id
 * @property int                     $number
 * @property int                     $seller_id
 * @property string                  $name
 * @property int|null                $proxy_id
 * @property string                  $address
 * @property CryptoCurrency          $currency
 * @property int                     $confirmations
 * @property CryptoWalletPaymentType $payment_type
 * @property string|null             $comment
 * @property Carbon|null             $created_at
 * @property Carbon|null             $updated_at
 * @property-read Collection|CryptoTransaction[] $cryptoTransactions
 * @property-read int|null $crypto_transactions_count
 * @property-read string $payment_type_readable
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Order[] $ordersWaitingPayment
 * @property-read int|null $orders_waiting_payment_count
 * @property-read Proxy|null $proxy
 * @property-read Seller $seller
 *
 * @method static Builder|CryptoWallet newModelQuery()
 * @method static Builder|CryptoWallet newQuery()
 * @method static Builder|CryptoWallet ofSeller(User $user, $sellerId = null)
 * @method static Builder|CryptoWallet query()
 * @method static Builder|CryptoWallet whereAddress($value)
 * @method static Builder|CryptoWallet whereComment($value)
 * @method static Builder|CryptoWallet whereConfirmations($value)
 * @method static Builder|CryptoWallet whereCreatedAt($value)
 * @method static Builder|CryptoWallet whereCurrency($value)
 * @method static Builder|CryptoWallet whereId($value)
 * @method static Builder|CryptoWallet whereName($value)
 * @method static Builder|CryptoWallet whereNumber($value)
 * @method static Builder|CryptoWallet wherePaymentType($value)
 * @method static Builder|CryptoWallet whereProxyId($value)
 * @method static Builder|CryptoWallet whereSellerId($value)
 * @method static Builder|CryptoWallet whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CryptoWallet extends Model implements Wallet
{
    use SellerIncrementId;

    /**
     * @var string[]
     */
    protected $casts = [
        'currency'     => CryptoCurrencyCast::class,
        'payment_type' => CryptoWalletPaymentTypeCast::class,
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'payment_type_readable',
    ];

    /**
     * @return HasMany
     */
    public function cryptoTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    /**
     * @return BelongsTo
     */
    public function proxy(): BelongsTo
    {
        return $this->belongsTo(Proxy::class);
    }

    /**
     * @return MorphMany
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'wallet');
    }

    /**
     * @return MorphMany
     */
    public function ordersWaitingPayment(): MorphMany
    {
        return $this->orders()
            ->whereIn(
                'status',
                [
                    OrderStatus::STATUS_AWAITING_PAYMENT,
                    OrderStatus::STATUS_PARTIALLY_PAID,
                ]
            );
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return route('bit.aps');
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function replaceTextVars(string $text): string
    {
        return $text;
    }

    /**
     * @param Order $order
     *
     * @throws BindingResolutionException
     */
    public function fillOrderInfo(Order $order): void
    {
        $rates = app()->make(GetCryptoPricingCommand::class)->execute();

        $order->crypto_uah_rate = $rates[$this->currency->getRubPair()]['buy_price'];

        if ($this->payment_type->getValue() === CryptoWalletPaymentType::ROTATE) {
            return;
        }

        $createPaymentAddressResponse = app()->make(CreateBitapsPaymentAddressCommand::class)
            ->execute(
                $this->currency,
                new CreateBitapsPaymentAddressDto($this->address, $this->getCallbackUrl(), $this->confirmations),
                $this->proxy
            );

        $order->bitaps_payment_address = $createPaymentAddressResponse->address;
        $order->bitaps_payment_code = $createPaymentAddressResponse->paymentCode;
        $order->bitaps_invoice = $createPaymentAddressResponse->invoice;
    }

    /**
     * @throws Throwable
     *
     * @return WalletType
     */
    public function getWalletType(): WalletType
    {
        $currencies = collect([
            CryptoCurrency::BTC => WalletType::TYPE_CRYPTO_BTC,
            CryptoCurrency::BCH => WalletType::TYPE_CRYPTO_BCH,
            CryptoCurrency::LTC => WalletType::TYPE_CRYPTO_LTC,
            CryptoCurrency::ETH => WalletType::TYPE_CRYPTO_ETH,
        ]);

        $getWalletType = $currencies->get($this->currency->getValue());

        return throw_unless(
            $walletType = new WalletType((int)$getWalletType),
            RuntimeException::class,
            ['message' => 'Unknown wallet type']
        );
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency->toMoneyCurrency();
    }

    /**
     * @return bool
     */
    public function isCrypto(): bool
    {
        return true;
    }

    /**
     * @param Order $order
     * @param Money $amount
     */
    public function createPaymentForGiven(Order $order, Money $amount): void
    {
        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $transaction = new CryptoTransaction();

        $transaction->seller_id = $order->seller_id;
        $transaction->crypto_wallet_id = $order->wallet->id;
        $transaction->order_id = $order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $amount;

        $transaction->save();
    }

    /**
     * @return string
     */
    public function getPaymentTypeReadableAttribute(): string
    {
        return $this->payment_type->getReadableValue();
    }
}

<?php

namespace App\Models\Wallet;

use App\Casts\EasyPayLoginDataCast;
use App\Casts\MoneyCast;
use App\Models\Order;
use App\Models\Proxy;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\Shift;
use App\Models\User;
use App\Services\Wallet\VO\WalletType;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Log;
use Money\Currency;
use Money\Money;

/**
 * App\Models\Wallet\EasyPayWallet.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property string      $name
 * @property int|null    $proxy_id
 * @property string      $phone
 * @property string      $password
 * @property int         $wrong_creadentials
 * @property int         $active
 * @property int         $wallet_number
 * @property int         $external_id
 * @property int         $instrument_id
 * @property Money       $limit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $restore_date
 * @property-read mixed $balance
 * @property-read mixed $is_limit
 * @property-read Proxy|null $proxy
 * @property-read Seller $seller
 * @property-read \Illuminate\Database\Eloquent\Collection|EasyPayTransaction[] $transactions
 * @property-read int|null $transactions_count
 *
 * @method static Builder|EasyPayWallet newModelQuery()
 * @method static Builder|EasyPayWallet newQuery()
 * @method static Builder|EasyPayWallet ofSeller(User $user, $sellerId = null)
 * @method static Builder|EasyPayWallet query()
 * @method static Builder|EasyPayWallet whereActive($value)
 * @method static Builder|EasyPayWallet whereCreatedAt($value)
 * @method static Builder|EasyPayWallet whereExternalId($value)
 * @method static Builder|EasyPayWallet whereId($value)
 * @method static Builder|EasyPayWallet whereInstrumentId($value)
 * @method static Builder|EasyPayWallet whereLimit($value)
 * @method static Builder|EasyPayWallet whereName($value)
 * @method static Builder|EasyPayWallet whereNumber($value)
 * @method static Builder|EasyPayWallet wherePassword($value)
 * @method static Builder|EasyPayWallet wherePhone($value)
 * @method static Builder|EasyPayWallet whereProxyId($value)
 * @method static Builder|EasyPayWallet whereRestoreDate($value)
 * @method static Builder|EasyPayWallet whereSellerId($value)
 * @method static Builder|EasyPayWallet whereUpdatedAt($value)
 * @method static Builder|EasyPayWallet whereWalletNumber($value)
 * @method static Builder|EasyPayWallet whereWrongCreadentials($value)
 * @mixin Eloquent
 */
class EasyPayWallet extends Model implements Wallet
{
    use SellerIncrementId;

    protected $casts = [
        'login_data'   => EasyPayLoginDataCast::class,
        'limit'        => MoneyCast::class,
        'restore_date' => 'datetime',
    ];

    public function proxy(): BelongsTo
    {
        return $this->belongsTo(Proxy::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(EasyPayTransaction::class);
    }

    public function replaceTextVars(string $text): string
    {
        return str_replace(
            [
                '{wallet-number}',
            ],
            [
                $this->wallet_number,
            ],
            $text
        );
    }

    public function fillOrderInfo(Order $order): void
    {
        // TODO: Implement fillOrderInfo() method.
    }

    public function getCurrency(): Currency
    {
        return new Currency('UAH');
    }

    public function isCrypto(): bool
    {
        return false;
    }

    public function getWalletType(): WalletType
    {
        return new WalletType(WalletType::TYPE_EASY_PAY);
    }

    public function createPaymentForGiven(Order $order, Money $amount): void
    {
        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $transaction = new EasyPayTransaction();

        $transaction->seller_id = $order->seller_id;
        $transaction->easy_pay_wallet_id = $order->wallet->id;
        $transaction->order_id = $order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $amount;

        $transaction->save();
    }

    /**
     * @return Money
     */
    public function getBalanceAttribute(): ?Money
    {
        try {
            return new Money(
                $this->transactions
                    ->when(
                        $this->restore_date,
                        fn (Collection $collection) => $collection
                        ->filter(
                            fn (EasyPayTransaction $transaction) => $transaction
                            ->created_at
                            ->greaterThan($this->restore_date)
                        )
                    )
                    ->map(fn (EasyPayTransaction $easyPayTransaction) => $easyPayTransaction->amount)
                    ->sum(fn (Money $money)                           => $money->getAmount()),
                new Currency('UAH')
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return new Money(0, new Currency('UAH'));
        }
    }

    /**
     * @return false
     */
    public function getIsLimitAttribute(): ?bool
    {
        try {
            if (!$this->balance->getAmount() || !$this->limit->getAmount()) {
                return false;
            }

            return $this->balance->greaterThanOrEqual($this->limit);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}

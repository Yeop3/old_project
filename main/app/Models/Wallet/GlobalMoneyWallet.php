<?php

namespace App\Models\Wallet;

use App\Casts\GlobalMoneyLoginDataCast;
use App\Models\Order;
use App\Models\Proxy;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\Shift;
use App\Models\User;
use App\Services\Wallet\VO\WalletType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Money\Currency;
use Money\Money;

/**
 * App\Models\Wallet\GlobalMoneyWallet.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property string      $name
 * @property int|null    $proxy_id
 * @property string      $login
 * @property string      $password
 * @property string      $type
 * @property string      $wallet_number
 * @property int         $active
 * @property int         $wrong_credentials
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Proxy|null $proxy
 * @property-read Seller $seller
 * @property-read Collection|GlobalMoneyTransaction[] $transactions
 * @property-read int|null $transactions_count
 *
 * @method static Builder|GlobalMoneyWallet newModelQuery()
 * @method static Builder|GlobalMoneyWallet newQuery()
 * @method static Builder|GlobalMoneyWallet ofSeller(User $user, $sellerId = null)
 * @method static Builder|GlobalMoneyWallet query()
 * @method static Builder|GlobalMoneyWallet whereActive($value)
 * @method static Builder|GlobalMoneyWallet whereCreatedAt($value)
 * @method static Builder|GlobalMoneyWallet whereId($value)
 * @method static Builder|GlobalMoneyWallet whereLogin($value)
 * @method static Builder|GlobalMoneyWallet whereName($value)
 * @method static Builder|GlobalMoneyWallet whereNumber($value)
 * @method static Builder|GlobalMoneyWallet wherePassword($value)
 * @method static Builder|GlobalMoneyWallet whereProxyId($value)
 * @method static Builder|GlobalMoneyWallet whereSellerId($value)
 * @method static Builder|GlobalMoneyWallet whereType($value)
 * @method static Builder|GlobalMoneyWallet whereUpdatedAt($value)
 * @method static Builder|GlobalMoneyWallet whereWalletNumber($value)
 * @method static Builder|GlobalMoneyWallet whereWrongCredentials($value)
 * @mixin Eloquent
 */
class GlobalMoneyWallet extends Model implements Wallet
{
    use SellerIncrementId;

    protected $casts = [
        'login_data' => GlobalMoneyLoginDataCast::class,
    ];

    public function proxy(): BelongsTo
    {
        return $this->belongsTo(Proxy::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GlobalMoneyTransaction::class);
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
        return new WalletType(WalletType::TYPE_GLOBAL_MONEY);
    }

    public function createPaymentForGiven(Order $order, Money $amount): void
    {
        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $transaction = new GlobalMoneyTransaction();

        $transaction->seller_id = $order->seller_id;
        $transaction->global_money_wallet_id = $order->wallet->id;
        $transaction->order_id = $order->id;
        $transaction->shift_id = $shift->id ?? null;
        $transaction->amount = $amount;

        $transaction->save();
    }
}

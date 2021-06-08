<?php

namespace App\Models\Wallet;

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
 * App\Models\Wallet\KunaAccount.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property string      $name
 * @property int|null    $proxy_id
 * @property string      $public_key
 * @property string      $private_key
 * @property bool        $active
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|KunaCode[] $kunaCodes
 * @property-read int|null $kuna_codes_count
 * @property-read Proxy|null $proxy
 * @property-read Seller $seller
 *
 * @method static Builder|KunaAccount newModelQuery()
 * @method static Builder|KunaAccount newQuery()
 * @method static Builder|KunaAccount ofSeller(User $user, $sellerId = null)
 * @method static Builder|KunaAccount query()
 * @method static Builder|KunaAccount whereActive($value)
 * @method static Builder|KunaAccount whereComment($value)
 * @method static Builder|KunaAccount whereCreatedAt($value)
 * @method static Builder|KunaAccount whereId($value)
 * @method static Builder|KunaAccount whereName($value)
 * @method static Builder|KunaAccount whereNumber($value)
 * @method static Builder|KunaAccount wherePrivateKey($value)
 * @method static Builder|KunaAccount whereProxyId($value)
 * @method static Builder|KunaAccount wherePublicKey($value)
 * @method static Builder|KunaAccount whereSellerId($value)
 * @method static Builder|KunaAccount whereUpdatedAt($value)
 * @mixin Eloquent
 */
class KunaAccount extends Model implements Wallet
{
    use SellerIncrementId;

    protected $casts = [
        'active' => 'boolean',
    ];

    public function kunaCodes(): HasMany
    {
        return $this->hasMany(KunaCode::class);
    }

    public function proxy(): BelongsTo
    {
        return $this->belongsTo(Proxy::class);
    }

    public function replaceTextVars(string $text): string
    {
        return $text;
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
        return new WalletType(WalletType::TYPE_KUNA_CODE);
    }

    public function createPaymentForGiven(Order $order, Money $amount): void
    {
        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $kunaCode = new KunaCode();

        $kunaCode->seller_id = $order->seller_id;
        $kunaCode->kuna_account_id = $order->wallet->id;
        $kunaCode->order_id = $order->id;
        $kunaCode->shift_id = $shift->id ?? null;
        $kunaCode->amount = $amount;

        $kunaCode->save();
    }
}

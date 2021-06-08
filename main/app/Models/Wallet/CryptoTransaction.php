<?php

namespace App\Models\Wallet;

use App\Casts\MoneyCast;
use App\Models\Order;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Money\Money;

/**
 * App\Models\Wallet\CryptoTransaction.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property int         $crypto_wallet_id
 * @property int         $order_id
 * @property int         $shift_id
 * @property string|null $address
 * @property string|null $hash
 * @property Money       $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CryptoWallet $cryptoWallet
 * @property-read string $format_around
 * @property-read Order $order
 * @property-read Seller $seller
 *
 * @method static Builder|CryptoTransaction newModelQuery()
 * @method static Builder|CryptoTransaction newQuery()
 * @method static Builder|CryptoTransaction ofSeller(User $user, $sellerId = null)
 * @method static Builder|CryptoTransaction query()
 * @method static Builder|CryptoTransaction whereAddress($value)
 * @method static Builder|CryptoTransaction whereAmount($value)
 * @method static Builder|CryptoTransaction whereCreatedAt($value)
 * @method static Builder|CryptoTransaction whereCryptoWalletId($value)
 * @method static Builder|CryptoTransaction whereHash($value)
 * @method static Builder|CryptoTransaction whereId($value)
 * @method static Builder|CryptoTransaction whereNumber($value)
 * @method static Builder|CryptoTransaction whereOrderId($value)
 * @method static Builder|CryptoTransaction whereSellerId($value)
 * @method static Builder|CryptoTransaction whereShiftId($value)
 * @method static Builder|CryptoTransaction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CryptoTransaction extends Model
{
    use SellerIncrementId;

    protected $appends = [
        'format_around',
    ];

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cryptoWallet(): BelongsTo
    {
        return $this->belongsTo(CryptoWallet::class);
    }

    public function getFormatAroundAttribute(): string
    {
        return formatMoney($this->amount).' '.$this->amount->getCurrency();
    }
}

<?php

namespace App\Models\Wallet;

use App\Casts\MoneyCast;
use App\Models\Order;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\Shift;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Money\Money;

/**
 * App\Models\Wallet\QiwiManualPayment.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $order_id
 * @property int         $shift_id
 * @property int         $qiwi_manual_wallet_id
 * @property int         $number
 * @property Money       $amount
 * @property string|null $client_wallet
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $sum
 * @property-read Order $order
 * @property-read Seller $seller
 * @property-read Shift $shift
 * @property-read QiwiManualWallet $wallet
 *
 * @method static Builder|QiwiManualPayment newModelQuery()
 * @method static Builder|QiwiManualPayment newQuery()
 * @method static Builder|QiwiManualPayment ofSeller(User $user, $sellerId = null)
 * @method static Builder|QiwiManualPayment query()
 * @method static Builder|QiwiManualPayment whereAmount($value)
 * @method static Builder|QiwiManualPayment whereClientWallet($value)
 * @method static Builder|QiwiManualPayment whereComment($value)
 * @method static Builder|QiwiManualPayment whereCreatedAt($value)
 * @method static Builder|QiwiManualPayment whereId($value)
 * @method static Builder|QiwiManualPayment whereNumber($value)
 * @method static Builder|QiwiManualPayment whereOrderId($value)
 * @method static Builder|QiwiManualPayment whereQiwiManualWalletId($value)
 * @method static Builder|QiwiManualPayment whereSellerId($value)
 * @method static Builder|QiwiManualPayment whereShiftId($value)
 * @method static Builder|QiwiManualPayment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class QiwiManualPayment extends Model
{
    use SellerIncrementId;

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    protected $appends = [
        'sum',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(QiwiManualWallet::class, 'qiwi_manual_wallet_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * @return string
     */
    public function getSumAttribute(): string
    {
        return formatMoney($this->amount);
    }
}

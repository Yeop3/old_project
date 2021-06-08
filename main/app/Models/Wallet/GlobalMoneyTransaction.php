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
 * App\Models\Wallet\GlobalMoneyTransaction.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property int         $global_money_wallet_id
 * @property int         $order_id
 * @property int         $shift_id
 * @property string|null $transaction_id
 * @property Money       $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read GlobalMoneyWallet $globalMoneyWallet
 * @property-read Order $order
 * @property-read Seller $seller
 *
 * @method static Builder|GlobalMoneyTransaction newModelQuery()
 * @method static Builder|GlobalMoneyTransaction newQuery()
 * @method static Builder|GlobalMoneyTransaction ofSeller(User $user, $sellerId = null)
 * @method static Builder|GlobalMoneyTransaction query()
 * @method static Builder|GlobalMoneyTransaction whereAmount($value)
 * @method static Builder|GlobalMoneyTransaction whereCreatedAt($value)
 * @method static Builder|GlobalMoneyTransaction whereGlobalMoneyWalletId($value)
 * @method static Builder|GlobalMoneyTransaction whereId($value)
 * @method static Builder|GlobalMoneyTransaction whereNumber($value)
 * @method static Builder|GlobalMoneyTransaction whereOrderId($value)
 * @method static Builder|GlobalMoneyTransaction whereSellerId($value)
 * @method static Builder|GlobalMoneyTransaction whereShiftId($value)
 * @method static Builder|GlobalMoneyTransaction whereTransactionId($value)
 * @method static Builder|GlobalMoneyTransaction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class GlobalMoneyTransaction extends Model
{
    use SellerIncrementId;

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function globalMoneyWallet(): BelongsTo
    {
        return $this->belongsTo(GlobalMoneyWallet::class);
    }
}

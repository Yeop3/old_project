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
 * App\Models\Wallet\EasyPayTransaction.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property int         $easy_pay_wallet_id
 * @property int         $order_id
 * @property int         $shift_id
 * @property string|null $transaction_id
 * @property Money       $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EasyPayWallet $easyPayWallet
 * @property-read Order $order
 * @property-read Seller $seller
 *
 * @method static Builder|EasyPayTransaction newModelQuery()
 * @method static Builder|EasyPayTransaction newQuery()
 * @method static Builder|EasyPayTransaction ofSeller(User $user, $sellerId = null)
 * @method static Builder|EasyPayTransaction query()
 * @method static Builder|EasyPayTransaction whereAmount($value)
 * @method static Builder|EasyPayTransaction whereCreatedAt($value)
 * @method static Builder|EasyPayTransaction whereEasyPayWalletId($value)
 * @method static Builder|EasyPayTransaction whereId($value)
 * @method static Builder|EasyPayTransaction whereNumber($value)
 * @method static Builder|EasyPayTransaction whereOrderId($value)
 * @method static Builder|EasyPayTransaction whereSellerId($value)
 * @method static Builder|EasyPayTransaction whereShiftId($value)
 * @method static Builder|EasyPayTransaction whereTransactionId($value)
 * @method static Builder|EasyPayTransaction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EasyPayTransaction extends Model
{
    use SellerIncrementId;

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function easyPayWallet(): BelongsTo
    {
        return $this->belongsTo(EasyPayWallet::class);
    }
}

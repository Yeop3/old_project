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
 * App\Models\Wallet\KunaCode.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property int         $kuna_account_id
 * @property int         $order_id
 * @property int         $shift_id
 * @property string|null $code
 * @property string|null $internal_id
 * @property string|null $sn
 * @property Money       $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read KunaAccount $kunaAccount
 * @property-read Order $order
 * @property-read Seller $seller
 * @property-read Shift $shift
 *
 * @method static Builder|KunaCode newModelQuery()
 * @method static Builder|KunaCode newQuery()
 * @method static Builder|KunaCode ofSeller(User $user, $sellerId = null)
 * @method static Builder|KunaCode query()
 * @method static Builder|KunaCode whereAmount($value)
 * @method static Builder|KunaCode whereCode($value)
 * @method static Builder|KunaCode whereCreatedAt($value)
 * @method static Builder|KunaCode whereId($value)
 * @method static Builder|KunaCode whereInternalId($value)
 * @method static Builder|KunaCode whereKunaAccountId($value)
 * @method static Builder|KunaCode whereNumber($value)
 * @method static Builder|KunaCode whereOrderId($value)
 * @method static Builder|KunaCode whereSellerId($value)
 * @method static Builder|KunaCode whereShiftId($value)
 * @method static Builder|KunaCode whereSn($value)
 * @method static Builder|KunaCode whereUpdatedAt($value)
 * @mixin Eloquent
 */
class KunaCode extends Model
{
    use SellerIncrementId;

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    public function kunaAccount(): BelongsTo
    {
        return $this->belongsTo(KunaAccount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}

<?php

namespace App\Models\Wallet;

use App\Models\Order;
use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\Shift;
use App\Models\User;
use App\Services\Order\VO\OrderStatus;
use App\Services\Wallet\VO\WalletType;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Money\Currency;
use Money\Money;

/**
 * App\Models\Wallet\QiwiManualWallet.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property string      $phone
 * @property bool        $active
 * @property int         $min_paid_orders_count
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Order[] $ordersAwaitingPayment
 * @property-read int|null $orders_awaiting_payment_count
 * @property-read Collection|Order[] $ordersPartiallyPaid
 * @property-read int|null $orders_partially_paid_count
 * @property-read Collection|QiwiManualPayment[] $payments
 * @property-read int|null $payments_count
 * @property-read Seller $seller
 *
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet ofSeller(User $user, $sellerId = null)
 * @method static Builder|QiwiManualWallet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereMinPaidOrdersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QiwiManualWallet whereUpdatedAt($value)
 * @method static Builder|QiwiManualWallet withTrashed()
 * @method static Builder|QiwiManualWallet withoutTrashed()
 * @mixin Eloquent
 */
class QiwiManualWallet extends Model implements Wallet
{
    use SellerIncrementId;
    use SoftDeletes;

    protected $casts = [
        'active' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(QiwiManualPayment::class);
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'wallet');
    }

    public function ordersAwaitingPayment(): MorphMany
    {
        return $this->morphMany(Order::class, 'wallet')->where('status', OrderStatus::STATUS_AWAITING_PAYMENT);
    }

    public function ordersPartiallyPaid(): MorphMany
    {
        return $this->morphMany(Order::class, 'wallet')->where('status', OrderStatus::STATUS_PARTIALLY_PAID);
    }

    public function replaceTextVars(string $text): string
    {
        return str_replace(
            [
                '{order-purse-phone}',
            ],
            [
                $this->phone,
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

    public function createPaymentForGiven(Order $order, Money $amount): void
    {
        $shift = Shift::whereSellerId($order->seller_id)->whereNull('ended_at')->first();

        $payment = new QiwiManualPayment();
        $payment->seller_id = $order->seller_id;
        $payment->order_id = $order->id;
        $payment->qiwi_manual_wallet_id = $order->wallet->id;
        $payment->shift_id = $shift->id ?? null;
        $payment->amount = $amount;

        $payment->save();
    }

    public function getWalletType(): WalletType
    {
        return new WalletType(WalletType::TYPE_QIWI_MANUAL);
    }
}

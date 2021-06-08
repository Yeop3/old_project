<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Casts\OrderStatusCast;
use App\Casts\PaymentMethodCast;
use App\Events\Order\OrderSavingEvent;
use App\Models\Wallet\Wallet;
use App\Services\Order\VO\OrderStatus;
use App\Services\ProductType\VO\DeliveryType;
use App\Services\Wallet\VO\CryptoWalletPaymentType;
use App\Services\Wallet\VO\PaymentMethod;
use App\Services\Wallet\VO\WalletType;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Money\Money;

/**
 * Class Order.
 *
 * @property Product productMaybeDeleted
 * @property int                             $id
 * @property int                             $seller_id
 * @property int                             $number
 * @property int|null                        $client_id
 * @property int                             $product_id
 * @property int                             $shift_id
 * @property int|null                        $discount_id
 * @property int                             $wallet_id
 * @property string                          $wallet_type
 * @property PaymentMethod                   $payment_method
 * @property int                             $source_id
 * @property string                          $source_type
 * @property OrderStatus                     $status
 * @property Money                           $unpaid_amount
 * @property Money                           $price
 * @property Money                           $total_price
 * @property Money                           $commission
 * @property Money                           $discount_amount
 * @property string|null                     $crypto_uah_rate
 * @property string|null                     $bitaps_payment_address
 * @property string|null                     $bitaps_payment_code
 * @property string|null                     $bitaps_invoice
 * @property int|null                        $found
 * @property int|null                        $not_found
 * @property int|null                        $rating
 * @property string|null                     $client_comment
 * @property string|null                     $name
 * @property string|null                     $packing
 * @property string|null                     $unit
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property-read Client|null $client
 * @property-read Discount|null $discount
 * @property-read string $created_at_diff
 * @property-read DeliveryType $delivery_type
 * @property-read string $delivery_type_readable
 * @property-read float|null $discount_value
 * @property-read bool $is_for_delivery
 * @property-read Money $paid_amount
 * @property-read string $payment_method_readable
 * @property-read mixed $product_maybe_deleted
 * @property-read string $updated_at_diff
 * @property-read Product $product
 * @property-read Seller $seller
 * @property-read Shift $shift
 * @property-read Model $source
 * @property-read Model|Wallet $wallet
 *
 * @method static Builder|Order clientNameFilter($clientName)
 * @method static Builder|Order dateFilter($date = null)
 * @method static Builder|Order dateRange(Carbon $dateStart = null, Carbon $dateEnd = null)
 * @method static Builder|Order inStatuses($statuses = null)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order numberFilter($number)
 * @method static Builder|Order ofSeller(User $user, $sellerId = null)
 * @method static Builder|Order orderFilter($order)
 * @method static Builder|Order orderStatus($orderStatus)
 * @method static Builder|Order paymentMethodFilter($paymentMethodId)
 * @method static Builder|Order productNameFilter($productName)
 * @method static Builder|Order productTypeFilter($productTypeId)
 * @method static Builder|Order query()
 * @method static Builder|Order sellerFilter($sellerId)
 * @method static Builder|Order sortFilter($sortDirection = null, $sortField = null)
 * @method static Builder|Order whereBitapsInvoice($value)
 * @method static Builder|Order whereBitapsPaymentAddress($value)
 * @method static Builder|Order whereBitapsPaymentCode($value)
 * @method static Builder|Order whereCanceledAt($value)
 * @method static Builder|Order whereClientComment($value)
 * @method static Builder|Order whereClientId($value)
 * @method static Builder|Order whereCommission($value)
 * @method static Builder|Order whereCompletedAt($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCryptoUahRate($value)
 * @method static Builder|Order whereDiscountAmount($value)
 * @method static Builder|Order whereDiscountId($value)
 * @method static Builder|Order whereFound($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereName($value)
 * @method static Builder|Order whereNotFound($value)
 * @method static Builder|Order whereNumber($value)
 * @method static Builder|Order wherePacking($value)
 * @method static Builder|Order wherePaymentMethod($value)
 * @method static Builder|Order wherePrice($value)
 * @method static Builder|Order whereProductId($value)
 * @method static Builder|Order whereRating($value)
 * @method static Builder|Order whereSellerId($value)
 * @method static Builder|Order whereShiftId($value)
 * @method static Builder|Order whereSourceId($value)
 * @method static Builder|Order whereSourceType($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereTotalPrice($value)
 * @method static Builder|Order whereUnit($value)
 * @method static Builder|Order whereUnpaidAmount($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereWalletId($value)
 * @method static Builder|Order whereWalletType($value)
 * @mixin Eloquent
 */
class Order extends Model
{
    use SellerIncrementId, SoftDeletes;

    protected $casts = [
        'unpaid_amount'   => MoneyCast::class,
        'price'           => MoneyCast::class,
        'total_price'     => MoneyCast::class,
        'commission'      => MoneyCast::class,
        'discount_amount' => MoneyCast::class,
        'status'          => OrderStatusCast::class,
        'payment_method'  => PaymentMethodCast::class,
    ];

    protected $dates = [
        'canceled_at',
        'completed_at',
        'created_at_diff',
        'updated_at_diff',
        'created_at',
        'updated_at',

    ];

    protected $dispatchesEvents = [
        'saving' => OrderSavingEvent::class,
    ];

    protected $appends = [
        'paid_amount',
        'created_at_diff',
        'updated_at_diff',
        'payment_method_readable',
    ];

    private bool $randomNumberGeneration = true;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * @return Product|Product[]|Collection|Model|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|mixed|null
     */
    public function getProductMaybeDeletedAttribute()
    {
        return $this->product;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function wallet(): MorphTo
    {
        return $this->morphTo();
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeOrderFilter(Builder $query, ?string $order): void
    {
        if ($order === 'shift_current') {
            $query->whereHas(
                'shift',
                fn (Builder $builder) => $builder->whereNull('shifts.ended_at')
            );

            return;
        }

        if ($order === 'shift_prev') {
            $query->whereHas(
                'shift',
                fn (Builder $builder) => $builder
                    ->whereNotNull('shifts.ended_at')
                    ->orderBy('started_at', 'desc')
            );

            return;
        }
    }

    public function scopeOrderStatus(Builder $query, ?int $orderStatus): void
    {
        if (!$orderStatus) {
            return;
        }

        $query->where('status', $orderStatus);
    }

    public function scopeClientNameFilter(Builder $query, ?string $clientName): void
    {
        if (!$clientName) {
            return;
        }

        $query->whereHas('client', function ($query) use ($clientName) {
            $query->whereHas('history', function ($q) use ($clientName) {
                $q->where('name', 'like', "%{$clientName}%")
                        ->orWhere('username', 'like', "%{$clientName}%");
            })
                ->orWhere('name', 'like', "%{$clientName}%")
                ->orWhere('username', 'like', "%{$clientName}%")
                ->orWhere('telegram_id', 'like', "%{$clientName}%");
        });
    }

    public function scopePaymentMethodFilter(Builder $query, ?int $paymentMethodId): void
    {
        if (!$paymentMethodId) {
            return;
        }

        $query->where('payment_method', $paymentMethodId);
    }

    public function scopeProductTypeFilter(Builder $query, ?int $productTypeId): void
    {
        if (!$productTypeId) {
            return;
        }

        $query->whereHas('product', fn ($q) => $q->where('product_type_id', $productTypeId));
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    public function scopeDateFilter(Builder $query, ?string $date = null): void
    {
        if (!$date) {
            return;
        }

        $query->whereDate('created_at', new Carbon($date));
    }

    public function scopeProductNameFilter(Builder $query, ?string $productName): void
    {
        if (!$productName) {
            return;
        }

        $query->where('name', 'like', "%{$productName}%");
    }

    public function scopeNumberFilter(Builder $query, ?int $number): void
    {
        if (!$number) {
            return;
        }

        $query->where('number', $number);
    }

    public function scopeDateRange(Builder $query, ?Carbon $dateStart = null, ?Carbon $dateEnd = null): void
    {
        if ($dateStart) {
            $query->whereDate('created_at', '>=', $dateStart);
        }

        if ($dateEnd) {
            $query->whereDate('created_at', '<', $dateEnd);
        }
    }

    public function scopeInStatuses(Builder $query, ?array $statuses = null): void
    {
        if (!$statuses) {
            return;
        }

        $query->whereIn('status', array_filter($statuses));
    }

    public function scopeSellerFilter(Builder $query, ?int $sellerId): Builder
    {
        if (!$sellerId) {
            return $query;
        }

        return $query->where('seller_id', $sellerId);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function getWalletType(): WalletType
    {
        return $this->wallet->getWalletType();
    }

    public function getReservationTimeLeft(): ?string
    {
        if ($this->status->getValue() !== OrderStatus::STATUS_AWAITING_PAYMENT) {
            return null;
        }

        $reservationSettingKey = $this->payment_method->getReservationTimeKey();

        $reservationTime = SellerSetting::whereSellerId($this->seller_id)->where('key', $reservationSettingKey)->first();

        return now()->longAbsoluteDiffForHumans($this->product->booked_at->addMinutes((int) $reservationTime->value), 2);
    }

    public function getPaidAmount(): Money
    {
        return $this->total_price->subtract($this->unpaid_amount);
    }

    public function getPaidAmountAttribute(): Money
    {
        return $this->getPaidAmount();
    }

    public function getCryptoUnpaidAmount(): Money
    {
        return convertToCryptoFromRub($this->unpaid_amount, $this->wallet->getCurrency(), $this->crypto_uah_rate);
    }

    public function getCryptoPaidAmount(?Money $uahPaidAmount = null): Money
    {
        return convertToCryptoFromRub($uahPaidAmount ?? $this->getPaidAmount(), $this->wallet->getCurrency(), $this->crypto_uah_rate);
    }

    public function getCryptoAddress(): ?string
    {
        if (!$this->wallet->isCrypto()) {
            return null;
        }

        if ($this->wallet->payment_type->getValue() === CryptoWalletPaymentType::BITAPS) {
            return $this->bitaps_payment_address;
        }

        return $this->wallet->address;
    }

    public function getDiscountValueAttribute(): ?float
    {
        if ($this->discount) {
            return $this->discount->discount_value->getValue();
        }

        if (!$this->client) {
            return 0.0;
        }

        return $this->client->discount_value->getValue();
    }

    public function getCreatedAtDiffAttribute(): string
    {
        if ($this->created_at) {
            return $this->created_at->diffForHumans();
        }

        return '';
    }

    public function getUpdatedAtDiffAttribute(): string
    {
        if ($this->updated_at) {
            return $this->updated_at->diffForHumans();
        }

        return '';
    }

    public function getPaymentMethodReadableAttribute(): string
    {
        return $this->payment_method->getReadableValue();
    }

    public function getFullName(): string
    {
        if ($this->product) {
            return "#{$this->number} / {$this->product->productType->getFullName()} / #{$this->product->number}";
        }

        if ($this->name) {
            return "#{$this->number} / $this->name ({$this->getPacking()})";
        }

        return "#{$this->number}";
    }

    public function getPacking(): string
    {
        if ($this->unit && $this->packing) {
            return "$this->packing $this->unit";
        }

        return '';
    }

    public function getFullText(): string
    {
        return "#{$this->number} {$this->getProductText()}";
    }

    public function getProductText(): string
    {
        $name = $this->product->productType->name;

        $unit = $this->product->productType->unit;
        $unitReadable = $unit->getReadableValue();

        $count = round($this->product->count, $unit->getRoundPrecision());

        return "{$name} {$count} {$unitReadable}";
    }

    public function getDeliveryTypeAttribute(): DeliveryType
    {
        return $this->product->delivery_type ?? $this->product_maybe_deleted->delivery_type;
    }

    public function getIsForDeliveryAttribute(): bool
    {
        return $this->delivery_type->isTaxi() || $this->delivery_type->isHotTreasure();
    }

    public function getDeliveryTypeReadableAttribute(): string
    {
        return $this->delivery_type->getReadableValue();
    }

    public function getReadableUnitAttribute(): string
    {
        return $this->product->productType->unit->getReadableValue();
    }
}

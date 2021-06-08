<?php

namespace App\Models;

use App\Casts\DiscountValueCast;
use App\Services\Discount\Calculator\DiscountCalculatorItem;
use App\Services\Discount\VO\DiscountValue;
use App\Services\Order\VO\OrderStatus;
use App\Services\ProductType\VO\DeliveryType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Client.
 *
 * @property int           $id
 * @property int           $seller_id
 * @property int           $number
 * @property string|null   $name
 * @property string|null   $username
 * @property int|null      $telegram_id
 * @property array|null    $info
 * @property int           $source_id
 * @property string        $source_type
 * @property string|null   $note
 * @property DiscountValue $discount_value
 * @property int           $discount_priority
 * @property Carbon|null   $ban_expires_at
 * @property bool          $in_black_list
 * @property array|null    $pre_order
 * @property Carbon|null   $created_at
 * @property Carbon|null   $updated_at
 * @property Carbon|null   $visited_at
 * @property Carbon|null   $deleted_at
 * @property-read Collection|Bot[] $bots
 * @property-read int|null $bots_count
 * @property-read Collection|Order[] $deliveryOrders
 * @property-read int|null $delivery_orders_count
 * @property-read Driver|null $driver
 * @property-read mixed $coming
 * @property-read string $label
 * @property-read Collection|ClientHistory[] $history
 * @property-read int|null $history_count
 * @property-read Order|null $lastOrder
 * @property-read Order|null $order
 * @property-read Order|null $orderAny
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Seller $seller
 * @property-read Model|Eloquent $source
 *
 * @method static Builder|Client dateFilter(Builder $query, ?string $date = null)
 * @method static Builder|Client nameFilter(Builder $query, ?string $name = null)
 * @method static Builder|Client ofSeller(User $user, $sellerId = null)
 * @method static Builder|Client discountFilter(Builder $query, ?int $value = null)
 * @method static Builder|Client newModelQuery()
 * @method static Builder|Client newQuery()
 * @method static Builder|Client blackListFilter(Builder $query, ?int $value = null)
 * @method static \Illuminate\Database\Query\Builder|Client onlyTrashed()
 * @method static Builder|Client query()
 * @method static Builder|Client whereBanExpiresAt($value)
 * @method static Builder|Client whereCreatedAt($value)
 * @method static Builder|Client whereDeletedAt($value)
 * @method static Builder|Client whereDiscountPriority($value)
 * @method static Builder|Client whereDiscountValue($value)
 * @method static Builder|Client whereId($value)
 * @method static Builder|Client whereInBlackList($value)
 * @method static Builder|Client whereInfo($value)
 * @method static Builder|Client whereName($value)
 * @method static Builder|Client whereNote($value)
 * @method static Builder|Client whereNumber($value)
 * @method static Builder|Client wherePreOrder($value)
 * @method static Builder|Client whereSellerId($value)
 * @method static Builder|Client whereSourceId($value)
 * @method static Builder|Client whereSourceType($value)
 * @method static Builder|Client whereTelegramId($value)
 * @method static Builder|Client whereUpdatedAt($value)
 * @method static Builder|Client whereUsername($value)
 * @method static Builder|Client whereVisitedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Client withoutTrashed()
 * @mixin Eloquent
 */
class Client extends Model
{
    use SoftDeletes;
    use SellerIncrementId;

    protected $casts = [
        'info'           => 'array',
        'discount_value' => DiscountValueCast::class,
        'in_black_list'  => 'boolean',
        'pre_order'      => 'array',
    ];

    protected $dates = [
        'visited_at',
        'ban_expires_at',
    ];

    protected $appends = [
        'coming',
        'label',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('status', OrderStatus::STATUS_IN_DELIVERY)
            ->whereHas(
                'product',
                fn (Builder $builder) => $builder->where('delivery_type', DeliveryType::TAXI)
            );
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereIn(
                'status',
                [OrderStatus::STATUS_AWAITING_PAYMENT, OrderStatus::STATUS_PARTIALLY_PAID]
            );
    }

    public function orderAny(): HasOne
    {
        return $this->hasOne(Order::class)->orderBy('created_at', 'desc');
    }

    public function scopeDateFilter(Builder $query, ?string $date = null): void
    {
        if (!$date) {
            return;
        }

        $query->whereDate('created_at', new Carbon($date));
    }

    public function scopeNameFilter(Builder $query, ?string $name = null): void
    {
        if (!$name) {
            return;
        }

        $query->where(
            fn (Builder $builder) => $builder
                ->where('name', 'like', "%$name%")
                ->orWhere('username', 'like', "%$name%")
                ->orWhere('telegram_id', 'like', "%$name%")
                ->orWhereHas('history', function ($q) use ($name) {
                    $q->where('name', 'like', "%$name%")
                        ->orWhere('username', 'like', "%$name%");
                })
        );
    }

    public function scopeDiscountFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('discount_value', $value);
    }

    public function scopeNoteFilter(Builder $query, ?string $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('note', 'like', "%$value%");
    }

    public function scopeNumberFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('number', $value);
    }

    public function scopeBlackListFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        if ($value === 1) {
            $query->where('in_black_list', 0);
        } elseif ($value === 2) {
            $query->where('in_black_list', 1);
        }
    }

    public function scopeBannedFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        if ($value === 1) {
            $query->where('ban_expires_at', date('Y-m-d'));
        } elseif ($value === 2) {
            $query->where('ban_expires_at');
        }
    }

    public function scopeBotFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->whereHas('bots', fn ($q) => $q->where('number', $value));
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    public function lastOrder(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereIn(
                'status',
                [
                    OrderStatus::STATUS_PAID,
                    OrderStatus::STATUS_GIVEN,
                ]
            )->orderByDesc('created_at');
    }

    public function getDiscountCalculatorItem(): DiscountCalculatorItem
    {
        return new DiscountCalculatorItem(
            $this->discount_value->getValue(),
            $this->discount_priority
        );
    }

    /**
     * @return mixed
     */
    public function getComingAttribute()
    {
        return $this->orders->map(
            fn (Order $order) => formatMoney($order->total_price->subtract($order->unpaid_amount))
        )->sum();
    }

    public function bots(): BelongsToMany
    {
        return $this->belongsToMany(Bot::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ClientHistory::class, 'client_id', 'id');
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * @param $value
     * @param $key
     *
     * @return false|string
     */
    public function check($value, $key)
    {
        if (!empty($value->{$key})) {
            return "$value($value)";
        }

        return false;
    }

    /**
     * @return string
     */
    public function getLabelAttribute(): string
    {
        if ($this->username) {
            return "$this->username($this->telegram_id)";
        }

        return $this->name ? "$this->name($this->telegram_id)" : $this->telegram_id;
    }
}

<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Shift.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property int|null    $operator_id
 * @property Carbon|null $started_at
 * @property Carbon|null $ended_at
 * @property-read Operator|null $operator
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Seller $seller
 *
 * @method static Builder|Shift current()
 * @method static Builder|Shift newModelQuery()
 * @method static Builder|Shift newQuery()
 * @method static Builder|Shift ofSeller(User $user, $sellerId = null)
 * @method static Builder|Shift query()
 * @method static Builder|Shift whereEndedAt($value)
 * @method static Builder|Shift whereId($value)
 * @method static Builder|Shift whereNumber($value)
 * @method static Builder|Shift whereOperatorId($value)
 * @method static Builder|Shift whereSellerId($value)
 * @method static Builder|Shift whereStartedAt($value)
 * @mixin Eloquent
 */
class Shift extends Model
{
    use SellerIncrementId;

    public $timestamps = false;

    public const SHIFT_DAY = 1;
    public const SHIFT_NIGHT = 2;

    public const SHIFT_NAMES = [
        self::SHIFT_DAY   => 'День',
        self::SHIFT_NIGHT => 'Ночь',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function orders(): HasMany
    {
        return  $this->hasMany(Order::class, 'shift_id', 'id');
    }

    public function scopeCurrent(Builder $builder): void
    {
        $builder->whereNull('ended_at');
    }

    public function scopeNumberFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('number', $value);
    }

    public function scopeOperatorFilter(Builder $query, ?int $value = null): void
    {
        $shiftName = self::SHIFT_NAMES[$value] ?? null;
        if (!$shiftName) {
            return;
        }

        $query->whereHas('operator', fn ($q) => $q->whereName($shiftName));
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }
}

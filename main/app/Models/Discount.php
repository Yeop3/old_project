<?php

namespace App\Models;

use App\Casts\DiscountDateRangeCast;
use App\Casts\DiscountValueCast;
use App\Services\Discount\VO\DiscountValue;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Discount.
 *
 * @property int           $id
 * @property int           $seller_id
 * @property int           $number
 * @property string        $name
 * @property string|null   $description
 * @property DiscountValue $discount_value
 * @property int           $discount_priority
 * @property bool          $active
 * @property mixed|null    $payment_methods
 * @property string|null   $date_start
 * @property string|null   $date_end
 * @property int           $client_min_paid_orders_count
 * @property int           $client_min_income
 * @property Carbon|null   $created_at
 * @property Carbon|null   $updated_at
 * @property Carbon|null   $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @property-read Collection|ProductType[] $productTypes
 * @property-read int|null $product_types_count
 * @property-read Seller $seller
 *
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount ofSeller(User $user, $sellerId = null)
 * @method static \Illuminate\Database\Query\Builder|Discount onlyTrashed()
 * @method static Builder|Discount query()
 * @method static Builder|Discount whereActive($value)
 * @method static Builder|Discount whereClientMinIncome($value)
 * @method static Builder|Discount whereClientMinPaidOrdersCount($value)
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereDateEnd($value)
 * @method static Builder|Discount whereDateStart($value)
 * @method static Builder|Discount whereDeletedAt($value)
 * @method static Builder|Discount whereDescription($value)
 * @method static Builder|Discount whereDiscountPriority($value)
 * @method static Builder|Discount whereDiscountValue($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereName($value)
 * @method static Builder|Discount whereNumber($value)
 * @method static Builder|Discount wherePaymentMethods($value)
 * @method static Builder|Discount whereSellerId($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Discount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Discount withoutTrashed()
 * @mixin Eloquent
 */
class Discount extends Model
{
    use SellerIncrementId;
    use SoftDeletes;

    protected $casts = [
        'discount_value' => DiscountValueCast::class,
        'date_range'     => DiscountDateRangeCast::class,
        'active'         => 'boolean',
    ];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    public function productTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProductType::class);
    }

    public function scopeNumberFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('number', $value);
    }

    public function scopeNameFilter(Builder $query, ?string $name = null): void
    {
        if (!$name) {
            return;
        }

        $query->where(
            fn (Builder $builder) => $builder
                ->where('name', 'like', "%$name%")
                ->orWhere('description', 'like', "%$name%")
        );
    }

    public function scopeDiscountFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('discount_value', $value);
    }

    public function scopeStatusFilter(Builder $query, ?int $value = null): void
    {
        if (!isset($value)) {
            return;
        }

        $query->where('active', $value);
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }
}

<?php

namespace App\Models;

use App\Casts\CommissionCast;
use App\Casts\DeliveryTypeCast;
use App\Casts\MoneyCast;
use App\Casts\UnitCast;
use App\Services\Discount\Calculator\DiscountCalculatorItem;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use App\VO\Unit;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Money\Money;

/**
 * App\Models\ProductType.
 *
 * @property int          $id
 * @property int          $seller_id
 * @property int          $number
 * @property string       $name
 * @property Money        $price
 * @property int          $commission_value
 * @property int          $commission_type
 * @property int|null     $packing
 * @property int|null     $real_packing
 * @property Unit|null    $unit
 * @property DeliveryType $delivery_type
 * @property int          $priority
 * @property array|null   $payment_methods
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $activeProducts
 * @property-read int|null $active_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Discount[] $discounts
 * @property-read int|null $discounts_count
 * @property-read array $location_numbers
 * @property-read \Kalnoy\Nestedset\Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Seller $seller
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType ofSeller(User $user, $sellerId = null)
 * @method static Builder|ProductType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereCommissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereCommissionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType wherePacking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType wherePaymentMethods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereRealPacking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductType whereUpdatedAt($value)
 * @method static Builder|ProductType withTrashed()
 * @method static Builder|ProductType withoutTrashed()
 * @mixin Eloquent
 */
class ProductType extends Model implements Discountable
{
    use SoftDeletes;
    use SellerIncrementId;

    protected $casts = [
        'price'           => MoneyCast::class,
        'commission'      => CommissionCast::class,
        'unit'            => UnitCast::class,
        'delivery_type'   => DeliveryTypeCast::class,
        'payment_methods' => 'array',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Product::class);
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('status', ProductStatus::STATUS_SELL);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountCalculatorItems(): Collection
    {
        return $this->discounts
            ->where('active', true)
            ->map(fn (Discount $discount) => new DiscountCalculatorItem(
                $discount->discount_value->getValue(),
                $discount->discount_priority,
                $discount->date_range,
                $discount->id
            ));
    }

    public function getFullName(bool $inLocation = false): string
    {
        if ($this->delivery_type->isTaxi()) {
            if ($inLocation) {
                return "$this->name (горячий клад)";
            }

            return "$this->name (горячий заказ)";
        }

        if (!$this->getPacking()) {
            return $this->name;
        }

        return "$this->name ({$this->getPacking()})";
    }

    public function getPacking(): ?string
    {
        if (!$this->unit || !$this->packing) {
            return null;
        }

        $unit = $this->unit->getReadableValue();

        return "$this->packing $unit";
    }

    public function getLocationNumbersAttribute(): array
    {
        return $this->locations->pluck('number')->toArray();
    }
}

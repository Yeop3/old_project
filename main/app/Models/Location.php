<?php

namespace App\Models;

use App\Casts\CommissionCast;
use App\Services\Discount\Calculator\DiscountCalculatorItem;
use App\VO\Commission;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * App\Models\Location.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property int         $_lft
 * @property int         $_rgt
 * @property int|null    $parent_id
 * @property string      $name
 * @property int         $priority
 * @property bool        $is_branch
 * @property int         $commission_value
 * @property int         $commission_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Kalnoy\Nestedset\Collection|Location[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Discount[] $discounts
 * @property-read int|null $discounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read array $chain
 * @property-read array $driver_numbers
 * @property-read string $name_chain
 * @property-read Location|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductType[] $productTypes
 * @property-read int|null $product_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Seller $seller
 * @property Commission|mixed $commission
 *
 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
 * @method static Builder|Location d()
 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
 * @method static QueryBuilder|Location newModelQuery()
 * @method static QueryBuilder|Location newQuery()
 * @method static Builder|Location ofSeller(User $user, $sellerId = null)
 * @method static QueryBuilder|Location query()
 * @method static Builder|Location whereCommissionType($value)
 * @method static Builder|Location whereCommissionValue($value)
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereIsBranch($value)
 * @method static Builder|Location whereLft($value)
 * @method static Builder|Location whereName($value)
 * @method static Builder|Location whereNumber($value)
 * @method static Builder|Location whereParentId($value)
 * @method static Builder|Location wherePriority($value)
 * @method static Builder|Location whereRgt($value)
 * @method static Builder|Location whereSellerId($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Location extends Model implements Discountable
{
    use NodeTrait;
    use SellerIncrementId;

    protected $casts = [
        'is_branch'  => 'boolean',
        'commission' => CommissionCast::class,
    ];

    public function getChain(): array
    {
        return $this->ancestors
            ->toFlatTree()
            ->pluck('name')
            ->push($this->name)
            ->toArray();
    }


    public function getNameChain(string $delimiter = ' -> '): string
    {
        return implode(' -> ', $this->getChain());
    }

    public function getChainAttribute(): array
    {
        return $this->getChain();
    }

    public function getNameChainAttribute(): string
    {
        return implode(' -> ', $this->chain);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'location_driver');
    }

    public function productTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProductType::class);
    }

    public function getDriverNumbersAttribute(): array
    {
        return $this->drivers->pluck('number')->toArray();
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
                $discount->id,
            ));
    }
}

<?php

namespace App\Models;

use App\Services\Driver\VO\PermissionTypes;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\Collection;

/**
 * App\Models\Driver.
 *
 * @property int $id
 * @property int $seller_id
 * @property int $number
 * @property string $name
 * @property int|null $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Bot[] $bots
 * @property-read int|null $bots_count
 * @property-read Client|null $client
 * @property-read Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @property-read Seller $seller
 * @property mixed permissions
 *
 * @method static Builder|Driver newModelQuery()
 * @method static Builder|Driver newQuery()
 * @method static Builder|Driver ofSeller(User $user, $sellerId = null)
 * @method static Builder|Driver query()
 * @method static Builder|Driver whereClientId($value)
 * @method static Builder|Driver whereCreatedAt($value)
 * @method static Builder|Driver whereId($value)
 * @method static Builder|Driver whereName($value)
 * @method static Builder|Driver whereNumber($value)
 * @method static Builder|Driver whereSellerId($value)
 * @method static Builder|Driver whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Driver extends Model {

    use SellerIncrementId;

    /**
     * @var string[]
     */
    protected $casts = [
        'permissions' => 'array',
    ];


    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'telegram_id', 'telegram_id');
    }

    /**
     * @return BelongsToMany
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_driver');
    }

//
//    public function getLocationNumbersAttribute(): array
//    {
//        return $this->locations->pluck('number')->toArray();
//    }

    /**
     * @return BelongsToMany
     */
    public function bots(): BelongsToMany
    {
        return $this->belongsToMany(Bot::class);
    }

    /**
     * @param Builder $query
     * @param int|null $value
     */
    public function scopeNumberFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('number', $value);
    }

    /**
     * @param Builder $query
     * @param string|null $name
     */
    public function scopeNameFilter(Builder $query, ?string $name = null): void
    {
        if (!$name) {
            return;
        }

        $query->where('name', 'like', "%$name%");
    }

    /**
     * @param Builder $query
     * @param string|null $sortDirection
     * @param string|null $sortField
     */
    public function scopeSortFilter(
        Builder $query,
        ?string $sortDirection = null,
        ?string $sortField = null
    ): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    /**
     * @param Builder $query
     */
    public function scopeCanCreate(Builder $query): void
    {
        $query->whereJsonContains('permissions', [
            PermissionTypes::CREATE_PRODUCT_TYPE,
        ]);
    }

    /**
     * @param Builder $query
     */
    public function scopeProcessTaxiOrder(Builder $query): void
    {
        $query->whereJsonContains('permissions', [
            PermissionTypes::PROCESS_TAXI_ORDER,
        ]);
    }

    /**
     * @param Builder $query
     */
    public function scopeProcessHotOrder(Builder $query): void
    {
        $query->whereJsonContains('permissions', [
            PermissionTypes::PROCESS_HOT_ORDER,
        ]);
    }

    /**
     * @return bool
     */
    public function canCreateProduct(): bool
    {
        return in_array(
            PermissionTypes::CREATE_PRODUCT_TYPE,
            $this->permissions ?? [],
            true
        );
    }

    /**
     * @return bool
     */
    public function canProcessTaxiOrder(): bool
    {
        return in_array(
            PermissionTypes::PROCESS_TAXI_ORDER,
            $this->permissions ?? [],
            true
        );
    }

    /**
     * @return bool
     */
    public function canProcessHotOrder(): bool
    {
        return in_array(
            PermissionTypes::PROCESS_HOT_ORDER,
            $this->permissions ?? [],
            true
        );
    }
}

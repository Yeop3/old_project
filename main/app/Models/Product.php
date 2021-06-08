<?php

namespace App\Models;

use App\Casts\CommissionCast;
use App\Casts\CoordinatesCast;
use App\Casts\DeliveryTypeCast;
use App\Casts\ProductStatusCast;
use App\Services\Coordinates\Coordinates;
use App\Services\Product\VO\ProductStatus;
use App\Services\ProductType\VO\DeliveryType;
use App\VO\Commission;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class Product.
 *
 * @property Commission       $comission
 * @property int              $id
 * @property int              $seller_id
 * @property int|null         $driver_id
 * @property int|null         $product_type_id
 * @property int|null         $location_id
 * @property int|null         $client_telegram_id
 * @property int              $number
 * @property int              $commission_value
 * @property int              $commission_type
 * @property string|null      $address
 * @property string|null      $image_src
 * @property string|null      $video_src
 * @property Coordinates|null $coordinates
 * @property ProductStatus    $status
 * @property DeliveryType     $delivery_type
 * @property string|null      $delivery_address
 * @property Carbon|null      $booked_at
 * @property Carbon|null      $created_at
 * @property Carbon|null      $updated_at
 * @property Carbon|null      $deleted_at
 * @property string|null      $count
 * @property Commission|mixed $commission
 * @property Carbon|null      $delivery_started_at
 * @property Carbon|null      $delivered_at
 * @property-read Client $client
 * @property-read Driver|null $driver
 * @property-read string $image_url
 * @property-read string $status_name
 * @property-read string|null $video_url
 * @property-read Location|null $location
 * @property-read Order $orders
 * @property-read Collection|ProductPhoto[] $photos
 * @property-read int|null $photos_count
 * @property-read ProductType|null $productType
 * @property-read Seller $seller
 *
 * @method static Builder|Product active()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product ofSeller(User $user, $sellerId = null)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product whereAddress($value)
 * @method static Builder|Product whereBookedAt($value)
 * @method static Builder|Product whereClientTelegramId($value)
 * @method static Builder|Product whereCommissionType($value)
 * @method static Builder|Product whereCommissionValue($value)
 * @method static Builder|Product whereCoordinates($value)
 * @method static Builder|Product whereCount($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereDeliveredAt($value)
 * @method static Builder|Product whereDeliveryAddress($value)
 * @method static Builder|Product whereDeliveryStartedAt($value)
 * @method static Builder|Product whereDeliveryType($value)
 * @method static Builder|Product whereDriverId($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereImageSrc($value)
 * @method static Builder|Product whereLocationId($value)
 * @method static Builder|Product whereNumber($value)
 * @method static Builder|Product whereProductTypeId($value)
 * @method static Builder|Product whereSellerId($value)
 * @method static Builder|Product whereStatus($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereVideoSrc($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin Eloquent
 */
class Product extends Model
{
    use SoftDeletes;
    use SellerIncrementId;

    protected $casts = [
        'status'        => ProductStatusCast::class,
        'commission'    => CommissionCast::class,
        'coordinates'   => CoordinatesCast::class,
        'delivery_type' => DeliveryTypeCast::class,
    ];

    protected $dates = [
        'booked_at',
        'delivery_started_at',
        'delivered_at',
    ];

    protected $appends = [
        'status_name',
        'image_url',
        'video_url',
    ];

    public function scopeActive(Builder $builder): void
    {
        $builder->where('status', ProductStatus::STATUS_SELL);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'telegram_id', 'client_telegram_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function getStatusNameAttribute(): string
    {
        return $this->status->getReadableValue();
    }

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'product_id', 'id');
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        return config('app.url').str_replace('//', '/', Storage::url($this->image_src));
    }

    /**
     * @return string|null
     */
    public function getVideoUrlAttribute(): ?string
    {
        if (!$this->video_src) {
            return null;
        }

        return config('app.url').str_replace('//', '/', Storage::url($this->video_src));
    }

    public function getAddressForBot(): string
    {
        if (!$this->address) {
            return '';
        }

        return PHP_EOL."Описание: $this->address".PHP_EOL;
    }
}

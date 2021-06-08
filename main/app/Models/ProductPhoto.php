<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductPhoto.
 *
 * @property int         $id
 * @property int         $number
 * @property int         $seller_id
 * @property int         $product_id
 * @property string      $src
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $url
 * @property-read Seller $seller
 *
 * @method static Builder|ProductPhoto newModelQuery()
 * @method static Builder|ProductPhoto newQuery()
 * @method static Builder|ProductPhoto ofSeller(User $user, $sellerId = null)
 * @method static Builder|ProductPhoto query()
 * @method static Builder|ProductPhoto whereCreatedAt($value)
 * @method static Builder|ProductPhoto whereId($value)
 * @method static Builder|ProductPhoto whereNumber($value)
 * @method static Builder|ProductPhoto whereProductId($value)
 * @method static Builder|ProductPhoto whereSellerId($value)
 * @method static Builder|ProductPhoto whereSrc($value)
 * @method static Builder|ProductPhoto whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProductPhoto extends Model
{
    use SellerIncrementId;

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): string
    {
        return config('app.url').str_replace('//', '/', Storage::url($this->src));
    }
}

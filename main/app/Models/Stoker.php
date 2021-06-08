<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Stoker.
 *
 * @property-read Client $client
 * @property-read Location $location
 * @property-read ProductType $productType
 * @property-read Seller $seller
 * @property-read Model|Eloquent $source
 *
 * @method static Builder|Stoker newModelQuery()
 * @method static Builder|Stoker newQuery()
 * @method static Builder|Stoker ofSeller(User $user, $sellerId = null)
 * @method static Builder|Stoker query()
 * @mixin Eloquent
 */
class Stoker extends Model
{
    use SellerIncrementId;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}

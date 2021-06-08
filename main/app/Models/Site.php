<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Site.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property string      $domain
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Seller $seller
 *
 * @method static Builder|Site newModelQuery()
 * @method static Builder|Site newQuery()
 * @method static Builder|Site ofSeller(User $user, $sellerId = null)
 * @method static Builder|Site query()
 * @method static Builder|Site whereCreatedAt($value)
 * @method static Builder|Site whereDeletedAt($value)
 * @method static Builder|Site whereDomain($value)
 * @method static Builder|Site whereId($value)
 * @method static Builder|Site whereNumber($value)
 * @method static Builder|Site whereSellerId($value)
 * @method static Builder|Site whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Site extends Model
{
    use SellerIncrementId;
}

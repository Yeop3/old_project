<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Operator.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property string      $name
 * @property string|null $telegram_name
 * @property string|null $telegram_id
 * @property int|null    $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null    $user_id
 * @property-read Client|null $client
 * @property-read Seller $seller
 * @property-read User|null $user
 *
 * @method static Builder|Operator newModelQuery()
 * @method static Builder|Operator newQuery()
 * @method static Builder|Operator ofSeller(User $user, $sellerId = null)
 * @method static Builder|Operator query()
 * @method static Builder|Operator whereClientId($value)
 * @method static Builder|Operator whereCreatedAt($value)
 * @method static Builder|Operator whereId($value)
 * @method static Builder|Operator whereName($value)
 * @method static Builder|Operator whereNumber($value)
 * @method static Builder|Operator whereSellerId($value)
 * @method static Builder|Operator whereTelegramId($value)
 * @method static Builder|Operator whereTelegramName($value)
 * @method static Builder|Operator whereUpdatedAt($value)
 * @method static Builder|Operator whereUserId($value)
 * @mixin Eloquent
 */
class Operator extends Model
{
    use SellerIncrementId;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'telegram_id', 'telegram_id');
    }
}

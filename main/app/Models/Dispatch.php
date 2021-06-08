<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Dispatch.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property int         $bot_id
 * @property string      $messages
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bot $bot
 * @property-read Seller $seller
 *
 * @method static Builder|Dispatch newModelQuery()
 * @method static Builder|Dispatch newQuery()
 * @method static Builder|Dispatch ofSeller(User $user, $sellerId = null)
 * @method static Builder|Dispatch query()
 * @method static Builder|Dispatch whereBotId($value)
 * @method static Builder|Dispatch whereCreatedAt($value)
 * @method static Builder|Dispatch whereId($value)
 * @method static Builder|Dispatch whereMessages($value)
 * @method static Builder|Dispatch whereNumber($value)
 * @method static Builder|Dispatch whereSellerId($value)
 * @method static Builder|Dispatch whereUpdatedAt($value)
 * @method static Builder|Order numberFilter($number)
 * @method static Builder|Order botFilter($number)
 * @method static Builder|Order messageFilter($number)
 * @method static Builder|Order sortFilter($number)
 * @mixin Eloquent
 */
class Dispatch extends Model
{
    use SellerIncrementId;

    /**
     * @return BelongsTo
     */
    public function bot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function scopeNumberFilter(Builder $query, ?int $number): void
    {
        if (!$number) {
            return;
        }

        $query->where('number', $number);
    }

    public function scopeBotFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->whereHas('bot', fn ($q) => $q->where('number', $value));
    }

    public function scopeMessageFilter(Builder $query, ?string $message): void
    {
        if (!$message) {
            return;
        }

        $query->where('messages', 'like', "%{$message}%");
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }
}

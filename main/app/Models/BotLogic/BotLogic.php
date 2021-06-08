<?php

namespace App\Models\BotLogic;

use App\Models\Seller;
use App\Models\SellerIncrementId;
use App\Models\User;
use App\Services\Bot\VO\BotLogicType;
use App\Services\Bot\VO\BotType;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogic.
 *
 * @property int         $id
 * @property int         $number
 * @property int|null    $seller_id
 * @property string      $name
 * @property string      $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|BotLogicAntispam[] $antispams
 * @property-read int|null $antispams_count
 * @property-read Collection|BotLogicCommand[] $commands
 * @property-read int|null $commands_count
 * @property-read Collection|BotLogicDistribution[] $distributions
 * @property-read int|null $distributions_count
 * @property-read Collection|BotLogicEvent[] $events
 * @property-read int|null $events_count
 * @property-read string $type
 * @property-read string $type_readable
 * @property-read Collection|BotLogicOperatorNotification[] $operatorNotifications
 * @property-read int|null $operator_notifications_count
 * @property-read Collection|BotLogicReminder[] $reminders
 * @property-read int|null $reminders_count
 * @property-read Seller|null $seller
 *
 * @method static Builder|BotLogic newModelQuery()
 * @method static Builder|BotLogic newQuery()
 * @method static Builder|BotLogic ofSeller(User $user, $sellerId = null)
 * @method static Builder|BotLogic query()
 * @method static Builder|BotLogic whereCreatedAt($value)
 * @method static Builder|BotLogic whereDescription($value)
 * @method static Builder|BotLogic whereId($value)
 * @method static Builder|BotLogic whereName($value)
 * @method static Builder|BotLogic whereNumber($value)
 * @method static Builder|BotLogic whereSellerId($value)
 * @method static Builder|BotLogic whereType(BotLogicType $type, $sellerId)
 * @method static Builder|BotLogic whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogic extends Model
{
    use SellerIncrementId;

    protected $appends = [
        'type',
        'type_readable',
    ];

    public function commands(): HasMany
    {
        return $this->hasMany(BotLogicCommand::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(BotLogicEvent::class);
    }

    public function operatorNotifications(): HasMany
    {
        return $this->hasMany(BotLogicOperatorNotification::class);
    }

    public function antispams(): HasMany
    {
        return $this->hasMany(BotLogicAntispam::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(BotLogicReminder::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(BotLogicDistribution::class);
    }

    public function getTypeAttribute(): string
    {
        return $this->seller_id === null ? BotType::STANDARD : BotType::CLIENT;
    }

    public function getTypeReadableAttribute(): string
    {
        return $this->seller_id === null ? 'standard' : 'client';
    }

    public function scopeWhereType(Builder $builder, BotLogicType $type, int $sellerId): void
    {
        if ($type->getValue() === BotLogicType::STANDARD) {
            $builder->whereNull('seller_id');

            return;
        }

        $builder->where('seller_id', $sellerId);
    }
}

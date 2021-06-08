<?php

namespace App\Models;

use App\Casts\BotLogicTypeCast;
use App\Casts\BotTypeCast;
use App\Casts\MessengerCast;
use App\Models\BotLogic\BotLogic;
use App\Services\Bot\VO\BotType;
use App\Services\Bot\VO\Messenger;
use BotMan\Drivers\Telegram\TelegramDriver;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Class Bot.
 *
 * @see MessengerCast $messenger
 *
 * @property int         $id
 * @property int         $seller_id
 * @property int         $number
 * @property string      $name
 * @property string      $username
 * @property string      $slug
 * @property string      $token
 * @property Messenger   $messenger
 * @property BotType     $type
 * @property int         $logic_id
 * @property string|null $operator_id
 * @property bool        $active
 * @property bool        $allow_create_clients
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read array $driver_numbers
 * @property-read BotLogic|null $logic
 * @property-read Operator|null $operator
 * @property-read Seller $seller
 *
 * @method static Builder|Bot newModelQuery()
 * @method static Builder|Bot newQuery()
 * @method static Builder|Bot ofSeller(User $user, $sellerId = null)
 * @method static \Illuminate\Database\Query\Builder|Bot onlyTrashed()
 * @method static Builder|Bot query()
 * @method static Builder|Bot standardTelegram()
 * @method static Builder|Bot whereActive($value)
 * @method static Builder|Bot whereAllowCreateClients($value)
 * @method static Builder|Bot whereCreatedAt($value)
 * @method static Builder|Bot whereDeletedAt($value)
 * @method static Builder|Bot whereId($value)
 * @method static Builder|Bot whereLogicId($value)
 * @method static Builder|Bot whereMessenger($value)
 * @method static Builder|Bot whereName($value)
 * @method static Builder|Bot whereNumber($value)
 * @method static Builder|Bot whereOperatorId($value)
 * @method static Builder|Bot whereSellerId($value)
 * @method static Builder|Bot whereSlug($value)
 * @method static Builder|Bot whereToken($value)
 * @method static Builder|Bot whereType($value)
 * @method static Builder|Bot whereUpdatedAt($value)
 * @method static Builder|Bot whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|Bot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Bot withoutTrashed()
 * @mixin Eloquent
 */
class Bot extends Model
{
    use SoftDeletes;
    use SellerIncrementId;

    protected $casts = [
        'messenger'            => MessengerCast::class,
        'type'                 => BotTypeCast::class,
        'logic_type'           => BotLogicTypeCast::class,
        'active'               => 'boolean',
        'allow_create_clients' => 'boolean',
    ];

    public function getWebHookUrl(): string
    {
        return config('app.url')."/webhook/$this->slug";
    }

    /**
     * @param Builder $builder
     */
    public function scopeStandardTelegram(Builder $builder): void
    {
        $builder
            ->where('messenger', Messenger::TELEGRAM)
            ->where('type', BotType::STANDARD);
    }

    public function scopeNumberFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('number', $value);
    }

    public function scopeNameFilter(Builder $query, ?string $name = null): void
    {
        if (!$name) {
            return;
        }

        $query->where(
            fn (Builder $builder) => $builder
                ->where('name', 'like', "%$name%")
                ->orWhere('username', 'like', "%$name%")
        );
    }

    public function scopeOperatorFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->where('operator_id', $value);
    }

    public function scopeDriverFilter(Builder $query, ?int $value = null): void
    {
        if (!$value) {
            return;
        }

        $query->whereHas('drivers', fn ($q) => $q->where('number', $value));
    }

    public function scopeSortFilter(Builder $query, ?string $sortDirection = null, ?string $sortField = null): void
    {
        if (($sortDirection === 'asc' || $sortDirection === 'desc') && $sortField) {
            $query->orderBy($sortField, $sortDirection);
        }
    }

    public function logic(): HasOne
    {
        return $this->hasOne(BotLogic::class, 'id', 'logic_id');
    }

    /**
     * @return BelongsTo
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * @return BelongsToMany
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class);
    }

    /**
     * @throws RuntimeException
     *
     * @return string
     */
    public function getBotDriver(): string
    {
        if ($this->messenger->getValue() === Messenger::TELEGRAM) {
            return TelegramDriver::class;
        }

        throw new RuntimeException('unknown bot driver for messenger '.$this->messenger->getReadableValue());
    }

    /**
     * @return BelongsToMany
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }

    /**
     * @return array
     */
    public function getDriverNumbersAttribute(): array
    {
        return $this->drivers->pluck('number')->toArray();
    }
}

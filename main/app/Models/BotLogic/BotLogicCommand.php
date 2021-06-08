<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicCommand.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property array       $keys
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|BotLogicCommandTemplate[] $templates
 * @property-read int|null $templates_count
 *
 * @method static Builder|BotLogicCommand newModelQuery()
 * @method static Builder|BotLogicCommand newQuery()
 * @method static Builder|BotLogicCommand query()
 * @method static Builder|BotLogicCommand whereBotLogicId($value)
 * @method static Builder|BotLogicCommand whereCreatedAt($value)
 * @method static Builder|BotLogicCommand whereId($value)
 * @method static Builder|BotLogicCommand whereKeys($value)
 * @method static Builder|BotLogicCommand whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicCommand extends Model
{
    protected $casts = [
        'keys' => 'array',
    ];

    protected $hidden = ['id', 'bot_logic_id'];

    public function templates(): HasMany
    {
        return $this->hasMany(BotLogicCommandTemplate::class);
    }
}

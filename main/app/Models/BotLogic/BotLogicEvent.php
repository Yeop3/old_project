<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicEvent.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property string      $key
 * @property string      $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicEvent newModelQuery()
 * @method static Builder|BotLogicEvent newQuery()
 * @method static Builder|BotLogicEvent query()
 * @method static Builder|BotLogicEvent whereBotLogicId($value)
 * @method static Builder|BotLogicEvent whereContent($value)
 * @method static Builder|BotLogicEvent whereCreatedAt($value)
 * @method static Builder|BotLogicEvent whereId($value)
 * @method static Builder|BotLogicEvent whereKey($value)
 * @method static Builder|BotLogicEvent whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicEvent extends Model
{
    protected $hidden = ['id', 'bot_logic_id'];
}

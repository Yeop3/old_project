<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicReminder.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property string      $key
 * @property array       $options
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicReminder newModelQuery()
 * @method static Builder|BotLogicReminder newQuery()
 * @method static Builder|BotLogicReminder query()
 * @method static Builder|BotLogicReminder whereBotLogicId($value)
 * @method static Builder|BotLogicReminder whereCreatedAt($value)
 * @method static Builder|BotLogicReminder whereId($value)
 * @method static Builder|BotLogicReminder whereKey($value)
 * @method static Builder|BotLogicReminder whereOptions($value)
 * @method static Builder|BotLogicReminder whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicReminder extends Model
{
    protected $casts = [
        'options' => 'array',
    ];

    protected $hidden = ['id', 'bot_logic_id'];
}

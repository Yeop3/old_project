<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicCommandTemplate.
 *
 * @property int         $id
 * @property int         $bot_logic_command_id
 * @property string      $key
 * @property string      $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicCommandTemplate newModelQuery()
 * @method static Builder|BotLogicCommandTemplate newQuery()
 * @method static Builder|BotLogicCommandTemplate query()
 * @method static Builder|BotLogicCommandTemplate whereBotLogicCommandId($value)
 * @method static Builder|BotLogicCommandTemplate whereContent($value)
 * @method static Builder|BotLogicCommandTemplate whereCreatedAt($value)
 * @method static Builder|BotLogicCommandTemplate whereId($value)
 * @method static Builder|BotLogicCommandTemplate whereKey($value)
 * @method static Builder|BotLogicCommandTemplate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicCommandTemplate extends Model
{
    protected $hidden = ['id', 'bot_logic_command_id'];
}

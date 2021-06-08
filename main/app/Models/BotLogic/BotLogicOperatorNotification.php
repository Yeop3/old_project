<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicOperatorNotification.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property string      $key
 * @property string      $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicOperatorNotification newModelQuery()
 * @method static Builder|BotLogicOperatorNotification newQuery()
 * @method static Builder|BotLogicOperatorNotification query()
 * @method static Builder|BotLogicOperatorNotification whereBotLogicId($value)
 * @method static Builder|BotLogicOperatorNotification whereContent($value)
 * @method static Builder|BotLogicOperatorNotification whereCreatedAt($value)
 * @method static Builder|BotLogicOperatorNotification whereId($value)
 * @method static Builder|BotLogicOperatorNotification whereKey($value)
 * @method static Builder|BotLogicOperatorNotification whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicOperatorNotification extends Model
{
    protected $hidden = ['id', 'bot_logic_id'];
}

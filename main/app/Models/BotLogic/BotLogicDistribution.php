<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicDistribution.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property string      $key
 * @property string      $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicDistribution newModelQuery()
 * @method static Builder|BotLogicDistribution newQuery()
 * @method static Builder|BotLogicDistribution query()
 * @method static Builder|BotLogicDistribution whereBotLogicId($value)
 * @method static Builder|BotLogicDistribution whereContent($value)
 * @method static Builder|BotLogicDistribution whereCreatedAt($value)
 * @method static Builder|BotLogicDistribution whereId($value)
 * @method static Builder|BotLogicDistribution whereKey($value)
 * @method static Builder|BotLogicDistribution whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicDistribution extends Model
{
    protected $hidden = ['id', 'bot_logic_id'];
}

<?php

namespace App\Models\BotLogic;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogic\BotLogicAntispam.
 *
 * @property int         $id
 * @property int         $bot_logic_id
 * @property string      $key
 * @property array       $options
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotLogicAntispam newModelQuery()
 * @method static Builder|BotLogicAntispam newQuery()
 * @method static Builder|BotLogicAntispam query()
 * @method static Builder|BotLogicAntispam whereBotLogicId($value)
 * @method static Builder|BotLogicAntispam whereCreatedAt($value)
 * @method static Builder|BotLogicAntispam whereId($value)
 * @method static Builder|BotLogicAntispam whereKey($value)
 * @method static Builder|BotLogicAntispam whereOptions($value)
 * @method static Builder|BotLogicAntispam whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotLogicAntispam extends Model
{
    protected $casts = [
        'options' => 'array',
    ];

    protected $hidden = ['id', 'bot_logic_id'];
}

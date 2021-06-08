<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotMessage.
 *
 * @property int         $id
 * @property int         $client_id
 * @property int         $bot_id
 * @property bool        $from_bot
 * @property string      $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|BotMessage newModelQuery()
 * @method static Builder|BotMessage newQuery()
 * @method static Builder|BotMessage query()
 * @method static Builder|BotMessage whereBotId($value)
 * @method static Builder|BotMessage whereClientId($value)
 * @method static Builder|BotMessage whereCreatedAt($value)
 * @method static Builder|BotMessage whereFromBot($value)
 * @method static Builder|BotMessage whereId($value)
 * @method static Builder|BotMessage whereText($value)
 * @method static Builder|BotMessage whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BotMessage extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'from_bot' => 'boolean',
    ];
}

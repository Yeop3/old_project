<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\MainBot.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $username
 * @property string      $slug
 * @property string      $token
 * @property bool        $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @method static Builder|MainBot newModelQuery()
 * @method static Builder|MainBot newQuery()
 * @method static Builder|MainBot query()
 * @method static Builder|MainBot whereActive($value)
 * @method static Builder|MainBot whereCreatedAt($value)
 * @method static Builder|MainBot whereDeletedAt($value)
 * @method static Builder|MainBot whereId($value)
 * @method static Builder|MainBot whereName($value)
 * @method static Builder|MainBot whereSlug($value)
 * @method static Builder|MainBot whereToken($value)
 * @method static Builder|MainBot whereUpdatedAt($value)
 * @method static Builder|MainBot whereUsername($value)
 * @mixin Eloquent
 */
class MainBot extends Model
{
    protected $casts = [
        'active' => 'boolean',
    ];

    public function getWebHookUrl(): string
    {
        return route('main.bot.hook', ['slug' => $this->getAttribute('slug')]);
    }
}

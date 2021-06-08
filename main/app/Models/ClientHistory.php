<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ClientHistory.
 *
 * @property int         $id
 * @property int         $client_id
 * @property string|null $name
 * @property string|null $username
 * @property array       $info
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ClientHistory newModelQuery()
 * @method static Builder|ClientHistory newQuery()
 * @method static Builder|ClientHistory query()
 * @method static Builder|ClientHistory whereClientId($value)
 * @method static Builder|ClientHistory whereCreatedAt($value)
 * @method static Builder|ClientHistory whereId($value)
 * @method static Builder|ClientHistory whereInfo($value)
 * @method static Builder|ClientHistory whereName($value)
 * @method static Builder|ClientHistory whereUpdatedAt($value)
 * @method static Builder|ClientHistory whereUsername($value)
 * @mixin Eloquent
 */
class ClientHistory extends Model
{
    protected $table = 'client_history';

    protected $casts = [
        'info' => 'array',
    ];
}

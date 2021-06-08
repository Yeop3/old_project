<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SellerSetting.
 *
 * @property int         $id
 * @property int         $seller_id
 * @property string      $section
 * @property string      $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|SellerSetting newModelQuery()
 * @method static Builder|SellerSetting newQuery()
 * @method static Builder|SellerSetting query()
 * @method static Builder|SellerSetting whereCreatedAt($value)
 * @method static Builder|SellerSetting whereId($value)
 * @method static Builder|SellerSetting whereKey($value)
 * @method static Builder|SellerSetting whereSection($value)
 * @method static Builder|SellerSetting whereSellerId($value)
 * @method static Builder|SellerSetting whereUpdatedAt($value)
 * @method static Builder|SellerSetting whereValue($value)
 * @mixin Eloquent
 */
class SellerSetting extends Model
{
    //
}

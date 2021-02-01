<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



/**
 * App\Ussd_flow
 *
 * @property int $id
 * @property string|null $message
 * @property string $input
 * @property string $level
 * @property string $sublevel1
 * @property int $session
 * @property string|null $code
 * @property string|null $telephone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Ussd_flow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereSublevel1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ussd_flow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Ussd_flow withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Ussd_flow withoutTrashed()
 * @mixin \Eloquent
 */
class Ussd_flow extends Model
{
    protected $table = 'ussd_flows';
}

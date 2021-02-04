<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Sms_outbox
 *
 * @property int $id
 * @property string|null $sender
 * @property string $message
 * @property string $telephone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Sms_outbox onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sms_outbox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Sms_outbox withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Sms_outbox withoutTrashed()
 * @mixin \Eloquent
 */
class Sms_outbox extends Model
{
	use SoftDeletes;
	protected $fillable=['message','sender','telephone','transaction'];
	
}

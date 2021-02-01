<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;/**
 * App\Payment
 *
 * @property int $id
 * @property string|null $code
 * @property string $tel
 * @property string|null $church_code
 * @property int|null $amount
 * @property string|null $fee_type
 * @property int $status
 * @property string $province
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Cooperative_member|null $cooperative_member
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereChurchCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
	use SoftDeletes;

 protected $guarded=['id'];
	
	public function member()
	{
		return $this->belongsTo(Member::class,'telephone','telephone')->where('status','1');
	}
	

}

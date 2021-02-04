<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * App\Member
 *
 * @property int $id
 * @property int|null $cooperative_id
 * @property string|null $code
 * @property string|null $church_code
 * @property string|null $church_group
 * @property int $status
 * @property string|null $new_code
 * @property string|null $photo
 * @property string $name
 * @property string $telephone
 * @property string|null $card_number
 * @property string|null $id_number
 * @property string|null $permit_number
 * @property float|null $share
 * @property string|null $gender
 * @property string|null $province
 * @property string|null $district
 * @property string|null $church
 * @property string|null $cell
 * @property string|null $village
 * @property string|null $zone
 * @property string|null $owner
 * @property string|null $vest_number
 * @property string|null $airtel_number
 * @property string|null $files
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Cooperative|null $cooperative
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\moto_detail[] $moto_detail
 * @property-read int|null $moto_detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Member[] $payment
 * @property-read int|null $payment_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Member onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereAirtelNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereCell($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereChurch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereChurchCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereChurchGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereNewCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member wherePermitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereVestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereVillage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Member whereZone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Member withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Member withoutTrashed()
 * @mixin \Eloquent
 */
class Member extends Model
{
use SoftDeletes;
	
	protected $guarded=['id'];

	public function payment()
	{
      return $this->hasMany(Payment::class,'telephone','telephone')->where('status','1');
	}
	public function category()
	{
      return $this->BelongsTo(Category::class);
	}
	public function getFullNameAttribute() {
        return ucfirst($this->first_name) . ' ' . strtoupper($this->last_name);
    }


}

<?php
namespace SED\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $full_name
 * @property string $photo
 * @property string $link
 */
class User extends Model
{
	protected $table = 'b_user';
	protected $primaryKey = 'ID';

	protected $visible = [
		'id',
		'full_name',
		'photo',
		'link',
	];

	protected $appends = ['id', 'full_name', 'photo', 'link'];

	public function getFullNameAttribute(): string
	{
		return trim($this->LAST_NAME . ' ' . $this->NAME . ' ' . $this->SECOND_NAME);
	}

	public function getPhotoAttribute()
	{
		$file = DB::table('b_file')->where('ID', $this->PERSONAL_PHOTO)->first();

		if ($file) {
			return 'https://bitrix.bsi.local/upload/' . $file->SUBDIR . '/' . $file->FILE_NAME;
		}

		return null;
	}

	public function getIdAttribute()
	{
		return $this->attributes['ID'];
	}

	public function getLinkAttribute()
	{
		return "https://bitrix.bsi.local/company/personal/user/{$this->attributes['ID']}/";
	}
}
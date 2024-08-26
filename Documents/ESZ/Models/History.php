<?php
namespace SED\Documents\ESZ\Models;

use SED\Documents\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $esz_id
 * @property int $user_id
 * @property string $event
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class History extends Model
{
	protected $table = 'l_esz_history';
	protected $with = ['user'];
	protected $fillable = [
		'event',
		'user_id',
		'esz_id',
	];

	public function user(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'user_id');
	}
}
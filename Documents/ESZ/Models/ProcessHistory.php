<?php
namespace SED\Documents\ESZ\Models;

use App\Modules\File\Models\File;
use SED\Documents\Common\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * @property int $id
 * @property int $esz_id
 * @property string $event
 * @property string $comment
 * @property int $user_id
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class ProcessHistory extends Model
{
	protected $table = 'l_esz_process_history';
	protected $fillable = [
		'event',
		'comment',
		'user_id',
		'esz_id',
	];
	protected $with = ['user', 'files'];

	public function user(): HasOne
	{
		return $this->hasOne(User::class, 'ID', 'user_id');
	}

	public function files(): BelongsToMany
	{
		return $this->belongsToMany(File::class, 'l_esz_process_history_files', 'history_id', 'file_id');
	}
}

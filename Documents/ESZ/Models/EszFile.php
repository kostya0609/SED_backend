<?php
namespace SED\Documents\ESZ\Models;

use App\Modules\File\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $type_id
 * @property int $file_id
 * @property int $esz_id
 * @property File $file
 */
class EszFile extends Model
{
	protected $table = 'l_esz_files';
	protected $with = [
		'file'
	];
	public $timestamps = false;

	public $fillable = [
		'type_id',
		'file_id',
		'esz_id',
	];

	public function file(): BelongsTo
	{
		return $this->belongsTo(File::class, 'file_id', 'id');
	}
}
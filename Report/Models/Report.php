<?php
namespace SED\Report\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $file_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Modules\File\Models\File $file
 */
class Report extends Model
{
	protected $table = 'l_sed_report';
	protected $with = ['file'];

	public function file(): BelongsTo
	{
		return $this->belongsTo(\App\Modules\File\Models\File::class);
	}
}
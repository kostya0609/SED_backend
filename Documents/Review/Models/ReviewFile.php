<?php
namespace SED\Documents\Review\Models;

use App\Modules\File\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $type_id
 * @property int $file_id
 * @property int $review_id
 */
class ReviewFile extends Model
{
	protected $table = 'l_review_files';
	public $timestamps = false;

    protected $with = ['file'];

	public $fillable = [
		'type_id',
		'file_id',
		'review_id',
	];

	public function file(): BelongsTo
	{
		return $this->belongsTo(File::class, 'file_id', 'id');
	}
}

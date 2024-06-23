<?php
namespace SED\Documents\Directive\Models;

use App\Modules\File\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $type_id
 * @property int $file_id
 * @property int $directive_id
 * @property File $file
 */
class DirectiveFile extends Model
{
	protected $table = 'l_directive_files';
	protected $with = [
		'file'
	];
	public $timestamps = false;

	public $fillable = [
		'type_id',
		'file_id',
		'directive_id',
	];

	public function file(): BelongsTo
	{
		return $this->belongsTo(File::class, 'file_id', 'id');
	}
}
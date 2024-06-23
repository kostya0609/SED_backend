<?php
namespace SED\Documents\Directive\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $content
 * @property string $portfolio
 */
class Contents extends Model
{
	protected $table = 'l_directive_content';
	protected $fillable = [
		'content',
		'portfolio',
	];
	protected $primaryKey = 'directive_id';
	public $timestamps = false;

	public function directive(): BelongsTo
	{
		return $this->belongsTo(Directive::class);
	}
}
<?php
namespace SED\Documents\Review\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $content
 * @property string $portfolio
 */
class Contents extends Model
{
	protected $table = 'l_review_content';
	protected $fillable = [
		'content',
		'portfolio',
	];

    protected $primaryKey = 'review_id';

	public $timestamps = false;

	public function review(): BelongsTo
	{
		return $this->belongsTo(Review::class);
	}
}

<?php
namespace SED\Documents\ESZ\Models;

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
	protected $table = 'l_esz_content';
	protected $fillable = [
		'content',
		'portfolio',
	];
	protected $primaryKey = 'esz_id';
	public $timestamps = false;
}
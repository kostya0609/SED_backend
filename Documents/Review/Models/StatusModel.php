<?php
namespace SED\Documents\Review\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $title
 */
class StatusModel extends Model
{
	protected $table = 'l_review_statuses';
	public $timestamps = false;
}

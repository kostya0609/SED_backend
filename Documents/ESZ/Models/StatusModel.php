<?php
namespace SED\Documents\ESZ\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $title
 */
class StatusModel extends Model
{
	protected $table = 'l_esz_statuses';
	public $timestamps = false;
}
